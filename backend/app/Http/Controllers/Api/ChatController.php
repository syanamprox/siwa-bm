<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Intelligence SIWA — AI Chat Intelligence (read-only).
 *
 * Port dari demo_distribution (garis d360_demo_businessplus), DENGAN PENYESUAIAN:
 * - READ-ONLY TOTAL: tanpa tool execute_sql, executeSql hanya SELECT/WITH.
 *   (Data kependudukan = PII warga — AI tidak boleh menulis apa pun.)
 * - Akses dibatasi role admin/camat/lurah via middleware di routes/api.php.
 * - System prompt domain SIWA: kependudukan (KK/warga), iuran RT, kas.
 *
 * Route: POST /api/intelligence/chat {message, chatSessionId?}
 * Route: GET  /api/intelligence/sessions
 * Route: GET  /api/intelligence/sessions/latest
 * Route: GET  /api/intelligence/sessions/{id}
 * Route: DELETE /api/intelligence/sessions/{id}
 */
class ChatController extends Controller
{
    /**
     * GET /sessions — list chat sessions (newest first).
     */
    public function sessions(Request $request)
    {
        $query = ChatSession::with('user:id,name,username')
            ->orderByDesc('updated_at')
            ->limit(30);

        // admin/lihat-semantic: semua session; role lain hanya miliknya
        // (praktisnya semua user yang lolos gate = admin/camat/lurah → lihat semua)
        $role = $request->user()->role;
        if (!in_array($role, ['admin', 'camat', 'lurah'])) {
            $query->where('user_id', $request->user()->id);
        }

        $sessions = $query->get(['id', 'user_id', 'title', 'can_write', 'created_at', 'updated_at']);

        return response()->json($sessions);
    }

    /**
     * GET /sessions/{id} — get one session with all messages.
     */
    public function showSession(Request $request, $id)
    {
        $session = ChatSession::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->with('messages')
            ->first();

        if (!$session) {
            return response()->json(['error' => 'not found'], 404);
        }

        return response()->json($session);
    }

    /**
     * GET /sessions/latest — the most recent session of the user (with messages).
     */
    public function latestSession(Request $request)
    {
        $session = ChatSession::where('user_id', $request->user()->id)
            ->with('messages')
            ->latest()
            ->first();

        if (!$session) {
            return response()->json(null);
        }

        return response()->json($session);
    }

    /**
     * DELETE /sessions/{id} — delete a session (owner only).
     */
    public function destroySession(Request $request, $id)
    {
        $session = ChatSession::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$session) {
            return response()->json(['error' => 'not found'], 404);
        }

        $session->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * POST /chat — send message, stream response via z.ai (Anthropic-compatible).
     * AI SELALU read-only di SIWA.
     */
    public function chat(Request $request)
    {
        $message = trim($request->input('message', ''));
        $chatSessionId = $request->input('chatSessionId');
        $user = $request->user();
        $userName = $user->name;

        if ($message === '') {
            return response()->json(['error' => 'message required'], 400);
        }

        // ── Load or create session ──
        if ($chatSessionId) {
            $session = ChatSession::where('user_id', $user->id)->where('id', $chatSessionId)->first();
            if (!$session) {
                return response()->json(['error' => 'session not found'], 404);
            }
        } else {
            $session = ChatSession::create([
                'user_id' => $user->id,
                'title' => mb_substr($message, 0, 50),
                'can_write' => false, // AI SELALU read-only (data kependudukan = PII)
            ]);
        }

        // Save user message
        ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $message,
        ]);

        // ── Config ──
        $baseUrl = rtrim(config('intelligence.base_url', 'https://api.z.ai/api/anthropic'), '/');
        $authToken = config('intelligence.auth_token') ?: config('intelligence.api_key');

        // Map model tier to actual model name
        $modelTier = config('intelligence.model', 'haiku');
        $model = match ($modelTier) {
            'sonnet' => config('intelligence.model_sonnet') ?: 'claude-sonnet-4-20250514',
            'opus'   => config('intelligence.model_opus') ?: 'claude-opus-4-20250514',
            default  => config('intelligence.model_haiku') ?: 'claude-3-5-haiku-20241022',
        };

        $systemPrompt = $this->buildSystemPrompt($userName);
        $tools = $this->buildTools();

        // ── Load conversation history — SEMUA pesan sesi ini, tanpa potongan ──
        // (AI selalu membaca konteks chat sebelumnya; tiap pesan = teks ringkas,
        //  aman utk context window GLM walau sesi panjang)
        $history = ChatMessage::where('chat_session_id', $session->id)
            ->orderBy('id')
            ->get(['role', 'content'])
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        $dbSession = $session;

        return response()->stream(function () use (
            $baseUrl, $authToken, $model, $systemPrompt, $tools, $history, $dbSession
        ) {
            @set_time_limit(0);
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            $apiMessages = $history;
            $fullText = '';
            $usedTools = [];
            $isError = false;
            $maxTurns = 6;
            $startTime = time();
            $overallTimeout = 120; // total max seconds across all turns

            for ($turn = 0; $turn < $maxTurns; $turn++) {
                // Total timeout check (across all turns)
                if ((time() - $startTime) > $overallTimeout) {
                    $fullText = "Maaf, pencarian ini memakan waktu terlalu lama. Coba pertanyaan yang lebih singkat dan spesifik ya.";
                    $isError = true;
                    break;
                }

                // Heartbeat sebelum setiap API call (keep connection alive)
                echo json_encode(['type' => 'heartbeat']) . "\n";
                flush();

                // Build API request
                $payload = [
                    'model' => $model,
                    'max_tokens' => 4096,
                    'system' => $systemPrompt,
                    'messages' => $apiMessages,
                    'tools' => $tools,
                    'stream' => true,
                ];

                // ── Stream API call + parse SSE ──
                $assistantContent = [];
                $stopReason = null;
                $sseBuffer = '';
                $currentBlock = null;
                $turnText = '';

                $onData = function ($ch, $chunk) use (
                    &$sseBuffer, &$currentBlock, &$assistantContent, &$stopReason,
                    &$turnText, &$usedTools
                ) {
                    $sseBuffer .= $chunk;

                    // Parse SSE events (separated by double newline)
                    while (($pos = strpos($sseBuffer, "\n\n")) !== false) {
                        $eventRaw = substr($sseBuffer, 0, $pos);
                        $sseBuffer = substr($sseBuffer, $pos + 2);

                        // Extract data: lines
                        $dataStr = '';
                        foreach (explode("\n", $eventRaw) as $line) {
                            if (str_starts_with($line, 'data:')) {
                                $dataStr .= substr($line, 5);
                            }
                        }
                        $dataStr = ltrim($dataStr);
                        if ($dataStr === '' || $dataStr === '[DONE]') continue;

                        $event = json_decode($dataStr, true);
                        if (!$event) continue;

                        $type = $event['type'] ?? '';

                        // ── Text streaming ──
                        if ($type === 'content_block_start') {
                            $block = $event['content_block'] ?? [];
                            $currentBlock = ['type' => $block['type'] ?? 'text'];
                            if (($block['type'] ?? '') === 'tool_use') {
                                $currentBlock['id'] = $block['id'] ?? '';
                                $currentBlock['name'] = $block['name'] ?? '';
                                $currentBlock['input_json'] = '';
                            } else {
                                $currentBlock['text'] = $block['text'] ?? '';
                            }
                        }

                        if ($type === 'content_block_delta') {
                            $delta = $event['delta'] ?? [];
                            if (($delta['type'] ?? '') === 'text_delta') {
                                $text = $delta['text'] ?? '';
                                $currentBlock['text'] = ($currentBlock['text'] ?? '') . $text;
                                $turnText .= $text;
                            } elseif (($delta['type'] ?? '') === 'input_json_delta') {
                                $currentBlock['input_json'] = ($currentBlock['input_json'] ?? '') . ($delta['partial_json'] ?? '');
                            }
                        }

                        if ($type === 'content_block_stop') {
                            if ($currentBlock) {
                                if ($currentBlock['type'] === 'text') {
                                    $assistantContent[] = [
                                        'type' => 'text',
                                        'text' => $currentBlock['text'],
                                    ];
                                } elseif ($currentBlock['type'] === 'tool_use') {
                                    $toolName = match ($currentBlock['name'] ?? '') {
                                        'query_database' => 'data',
                                        default => $currentBlock['name'] ?? 'tool',
                                    };
                                    if (!in_array($toolName, $usedTools)) {
                                        $usedTools[] = $toolName;
                                        echo json_encode(['type' => 'tool', 'name' => $toolName]) . "\n";
                                        flush();
                                    }
                                    $assistantContent[] = [
                                        'type' => 'tool_use',
                                        'id' => $currentBlock['id'],
                                        'name' => $currentBlock['name'],
                                        'input' => json_decode($currentBlock['input_json'], true) ?: [],
                                    ];
                                }
                            }
                            $currentBlock = null;
                        }

                        if ($type === 'message_delta') {
                            $stopReason = $event['delta']['stop_reason'] ?? $stopReason;
                        }
                    }

                    return strlen($chunk);
                };

                $ch = curl_init($baseUrl . '/v1/messages');
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'x-api-key: ' . $authToken,
                        'anthropic-version: 2023-06-01',
                    ],
                    CURLOPT_WRITEFUNCTION => $onData,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_CONNECTTIMEOUT => 15,
                ]);

                curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                // ── Error handling ──
                if ($httpCode !== 200 || !empty($curlError)) {
                    if (!empty($curlError)) {
                        if (str_contains($curlError, 'timed out') || str_contains($curlError, 'Timeout')) {
                            $errMsg = "server AI membutuhkan waktu terlalu lama. Coba ulangi sebentar lagi.";
                        } else {
                            $errMsg = "koneksi ke server AI terputus. Coba lagi ya.";
                        }
                    } elseif ($httpCode === 401 || $httpCode === 403) {
                        $errMsg = "ada masalah autentikasi dengan server AI. Tim teknis akan segera menanganinya.";
                    } elseif ($httpCode === 429) {
                        $errMsg = "server AI sedang sibuk. Coba lagi dalam 1-2 menit ya.";
                    } elseif ($httpCode >= 500) {
                        $errMsg = "server AI sedang mengalami gangguan. Coba lagi nanti ya.";
                    } else {
                        $errMsg = "terjadi kesalahan saat memproses. Coba pertanyaan yang lebih singkat.";
                    }
                    $fullText = empty($fullText)
                        ? "Maaf, {$errMsg}"
                        : $fullText . "\n\n⚠️ Sebagian data mungkin belum lengkap — {$errMsg}";
                    $isError = true;
                    break;
                }

                // Add assistant response to messages for next turn
                $apiMessages[] = ['role' => 'assistant', 'content' => $assistantContent];

                // If no tool use, this is the final response — keep turnText
                if ($stopReason !== 'tool_use') {
                    $fullText = $turnText;
                    break;
                }

                // Tool-use turn: discard preliminary text, continue to next turn
                $turnText = '';

                // ── Execute tools ──
                $toolResults = [];
                foreach ($assistantContent as $block) {
                    if (($block['type'] ?? '') !== 'tool_use') continue;

                    $sql = $block['input']['sql'] ?? '';
                    $result = $this->executeSql($sql);
                    $toolResults[] = [
                        'type' => 'tool_result',
                        'tool_use_id' => $block['id'],
                        'content' => $result,
                    ];
                }

                // Add tool results as next user message
                $apiMessages[] = ['role' => 'user', 'content' => $toolResults];
            }

            // ── Handle empty response (loop exhaustion or empty final turn) ──
            if ($fullText === '') {
                $fullText = !empty($usedTools)
                    ? "Maaf, saya sudah mencari datanya tapi belum ketemu jawabannya. Coba lebih spesifik ya — misalnya sebutkan bulan atau RT tertentu."
                    : "Maaf, saya belum bisa menjawab itu. Bisakah diulang dengan kata-kata yang berbeda?";
                $isError = true;
            }

            // ── Handle response cut off by max_tokens ──
            if ($stopReason === 'max_tokens') {
                $fullText .= "\n\n*(Respons terpotong karena terlalu panjang — coba pertanyaan yang lebih spesifik)*";
            }

            // ── Stream final text to frontend in batches (smooth typing effect) ──
            if ($fullText !== '') {
                $total = mb_strlen($fullText);
                $chunkSize = max(20, (int)ceil($total / 8));
                for ($i = 0; $i < $total; $i += $chunkSize) {
                    $chunk = mb_substr($fullText, $i, $chunkSize);
                    echo json_encode(['type' => 'text', 'text' => $chunk], JSON_UNESCAPED_UNICODE) . "\n";
                    flush();
                }
            }

            // ── Save assistant message to DB ──
            ChatMessage::create([
                'chat_session_id' => $dbSession->id,
                'role' => 'assistant',
                'content' => $fullText,
                'tools' => $usedTools ?: null,
                'is_error' => $isError,
            ]);

            $dbSession->touch();

            echo json_encode([
                'type' => 'done',
                'text' => $fullText,
                'tools' => $usedTools,
                'isError' => $isError,
                'chatSessionId' => $dbSession->id,
                'canWrite' => false, // AI selalu read-only
            ]) . "\n";
            flush();
        }, 200, [
            'Content-Type' => 'application/x-ndjson',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Execute SQL directly via Laravel DB facade.
     * READ-ONLY TOTAL: hanya SELECT/WITH yang dieksekusi — tanpa pengecualian.
     */
    private function executeSql(string $sql): string
    {
        $sql = trim($sql);
        $sql = preg_replace('/;+\s*$/', '', $sql);

        if ($sql === '') {
            return json_encode(['error' => 'empty SQL']);
        }

        // Block multiple statements
        if (str_contains($sql, ';')) {
            return json_encode(['error' => 'one statement only']);
        }

        // Block destructive operations
        if (preg_match('/\b(drop|alter|create|truncate|grant|revoke|insert|update|delete|replace)\b/i', $sql)) {
            return json_encode(['error' => 'read-only access. Only SELECT allowed.']);
        }

        try {
            // Auto-add LIMIT for SELECT
            if (!preg_match('/\blimit\s+\d+/i', $sql)) {
                $sql .= ' LIMIT 100';
            }
            $rows = DB::select($sql);

            return json_encode([
                'rows' => $rows,
                'rowCount' => count($rows),
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Build Anthropic tool definitions for the API request.
     * READ-ONLY: hanya query_database.
     */
    private function buildTools(): array
    {
        return [
            [
                'name' => 'query_database',
                'description' => 'Execute a read-only SQL SELECT query on the SIWA database (data kependudukan, iuran, kas). Returns JSON {"rows":[...],"rowCount":N} or {"error":"..."}.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'sql' => [
                            'type' => 'string',
                            'description' => 'SQL SELECT or WITH statement. LIMIT 100 auto-added if not specified.',
                        ],
                    ],
                    'required' => ['sql'],
                ],
            ],
        ];
    }

    /**
     * System prompt — domain SIWA (Sistem Informasi Warga & Administrasi kelurahan
     * Bendul Merisi, Kec. Wonocolo, Surabaya). AI selalu read-only.
     */
    private function buildSystemPrompt(string $userName): string
    {
        $today = now()->setTimezone('Asia/Jakarta')->format('l, d F Y');
        $isoToday = now()->setTimezone('Asia/Jakarta')->toDateString();

        return <<<PROMPT
Kamu adalah "Intelligence SIWA", asisten AI internal di aplikasi SIWA — sistem informasi warga & administrasi Kelurahan Bendul Merisi (Kecamatan Wonocolo, Surabaya). Modul: Kependudukan (Kartu Keluarga & warga), Iuran RT (jenis iuran, tagihan bulanan per KK, pembayaran), Kas (buku kas RT/RW/kelurahan & organisasi: Karang Taruna, Musholla, Rukem Sehati, PKK, Sosial), Laporan, Wilayah (Kelurahan → RW → RT).

🚫 ATURAN MUTLAK #1 — WAJIB QUERY SEBELUM JAWAB:
Untuk SETIAP pertanyaan yang melibatkan angka, data, statistik, atau fakta kependudukan/keuangan, kamu WAJIB memanggil tool query_database TERLEBIH DAHULU sebelum menjawab. TIDAK ADA PENGECUALIAN.
- DILARANG menjawab "menurut sistem..." tanpa benar-benar query database lebih dulu.
- DILARANG mengarang, mengestimasi, atau menebak angka. Jika tidak yakin, QUERY LAGI.
- Angka yang kamu sebut HARUS persis sama dengan hasil query — termasuk angka desimalnya.
- Jika query return Rp 410597.10 → tulis Rp 410.597 (atau Rp 410.597,10 untuk detail).
- Total/jumlah: biarkan database yang hitung dengan SUM()/COUNT(). JANGAN jumlahkan manual di kepala.

AKSES: BACA SAJA. Kamu hanya bisa MEMBACA data dengan tool query_database. JANGAN pernah mencoba mengubah data (tidak ada tool tulis). Jika user minta mengubah/menambah/menghapus data, jelaskan dengan sopan bahwa kamu hanya bisa membaca — perubahan data dilakukan lewat menu aplikasi SIWA.

CARA MENJAWAB:
- Pakai Bahasa Indonesia yang natural dan mudah dipahami.
- JANGAN gunakan istilah teknis seperti "query", "database", "SQL", "tabel" kepada user.
- Langsung beri jawaban akhir, jangan jelaskan proses pencarian data.
- Tebalkan angka penting pakai **...**.
- Nominal uang pakai format Rupiah (contoh: Rp 8,7 juta, atau Rp 8.747.160 untuk detail).
- Banyak angka → pakai bullet atau tabel rapi.

FORMAT JAWABAN (WAJIB):
- Markdown penuh (GFM): heading, bullet, **bold**, tabel markdown asli dengan garis pipa.
- Jawaban berupa daftar Top-N, perbandingan, rincian per RT/kategori, atau aging → WAJIB tabel markdown.
- DILARANG membuat tabel ASCII di dalam code block — tabel selalu format markdown asli.
- Jika user minta grafik, ATAU data cocok divisualisasikan (tren bulanan, komposisi, perbandingan Top-N), tambahkan blok chart SEBELUM/Sesudah tabel teks, dengan format persis:
  \`\`\`chart
  {"type":"bar","title":"Judul grafik","data":[{"label":"Label 1","value":123},...]}
  \`\`\`
  - type: "bar" (perbandingan/Top-N), "pie" (komposisi, maks 6 titik), "line" (tren waktu).
  - value WAJIB angka polos TANPA format (2097972720, bukan "Rp 2,09 M").
  - Maksimal 8 titik data; sisanya digabung jadi label "Lainnya".
  - Jangan pakai blok chart kalau datanya cuma 1-2 angka.

PERTANYAAN DI LUAR TOPIK SIWA:
- Kamu HANYA asisten untuk sistem SIWA kelurahan. BUKAN ensiklopedia, bukan asisten umum.
- Jika ditanya hal di luar sistem SIWA (sejarah, tokoh, resep, sains, gosip, dll): jawab singkat "Maaf, saya hanya melayani pertanyaan seputar data kelurahan Bendul Merisi — kependudukan, iuran RT, kas, dan laporan. Ada yang bisa saya bantu soal itu?"
- DILARANG membahas agama, keyakinan, atau opini politik — apa pun alasannya.
- Jika user maksa atau provokatif, tetap sopan dan tetap redirect ke topik SIWA.
- Data warga bersifat sensitif: jika user minta daftar NIK/nomor KK mentah dalam jumlah besar, berikan ringkasan (jumlah, sebaran) dan ingatkan data identitas lengkap dilihat lewat menu Kependudukan.

SKEMA DATA (MySQL — nama tabel persis):
PENTING: sebagian besar tabel memakai Laravel soft delete (kolom deleted_at) — SELALU tambahkan WHERE deleted_at IS NULL pada tabel: users, wilayahs, keluargas, wargas, jenis_iurans, keluarga_iuran, iurans, kas_units. (pembayaran_iurans, kas_transaksis, aktivitas_logs TIDAK punya deleted_at.)

WILAYAH & PENGGUNA:
- wilayahs (id, kode, nama "RT 01 RW 03 Bendul Merisi"/"RW 03 Bendul Merisi", tingkat[Kelurahan|RW|RT], parent_id) — hierarki: Kelurahan → RW → RT. id kelurahan Bendul Merisi = akar.
- users (id, name, username, role[admin|camat|lurah|rw|rt], status_aktif, created_at) — petugas; penugasan wilayah di user_wilayahs (user_id, wilayah_id)

KEPENDUDUKAN:
- keluargas (id, no_kk 16-digit, kepala_keluarga_id→wargas.id, alamat_kk, status_keluarga, rt_kk, rw_kk, kelurahan_kk, alamat_domisili, rt_id→wilayahs.id (RT domisili), status_domisili_keluarga[Tetap|Domisili|Non Domisili|Pendatang], status_miskin[Miskin|Pra-Miskin|Non], is_verified, verified_at, foto_kk, foto_rumah, keterangan_status)
- wargas (id, nik 16-digit unique, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin[L|P], agama, status_perkawinan, pekerjaan, pendidikan_terakhir, kk_id→keluargas.id, hubungan_keluarga[Kepala Keluarga|Istri|Anak|Cucu|Famili Lain|...], no_telepon, nama_ayah, nama_ibu, meninggal (0=hidup, 1=alm), tanggal_meninggal, is_verified, created_at)
  - Umur = TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()); utk alm beku di tanggal_meninggal.

IURAN RT:
- jenis_iurans (id, nama, kode, jumlah, periode[bulanan|tahunan|sekali], is_aktif, sasaran[semua|kk|warga], rt_id→wilayahs.id)
- keluarga_iuran (id, keluarga_id, jenis_iuran_id, nominal_custom — override nominal per KK, status_aktif, alasan_custom)
- iurans (id, kk_id→keluargas.id, jenis_iuran_id, periode_bulan "YYYY-MM", nominal, status[belum_bayar|lunas], jatuh_tempo, keterangan) — SATU baris = tagihan satu KK satu jenis iuran satu bulan
- pembayaran_iurans (id, iuran_id, jumlah_bayar, metode_pembayaran[cash|transfer|qris|ewallet], nomor_referensi, created_at) — bisa lebih dari satu pembayaran per tagihan
  - Tunggakan = SUM(nominal) iurans WHERE status='belum_bayar'
  - Nominal efektif per tagihan: pakai iurans.nominal (sudah final saat generate).

KAS:
- kas_units (id, nama, jenis[rt|rw|kelurahan|kecamatan|organisasi], wilayah_id)
- kas_transaksis (id, kas_unit_id, tipe[masuk|keluar], sumber[iuran|manual], pembayaran_iuran_id, jumlah, kategori, keterangan, tanggal, created_at)
  - kategori: Iuran, Donasi, Parkir, Infaq, Saldo Awal, Operasional, Rapat, Perlengkapan, Kesehatan, Kegiatan, Pemakaman, Lain-lain
  - Saldo unit = SUM(CASE WHEN tipe='masuk' THEN jumlah ELSE -jumlah END) dengan Saldo Awal ikut masuk.
  - sumber='iuran' = posting otomatis dari pembayaran iuran RT; sumber='manual' = catatan bendahara.

POLA QUERY (contoh SQL yang benar):

• Jumlah warga hidup:
  SELECT COUNT(*) total FROM wargas WHERE deleted_at IS NULL AND meninggal = 0

• Warga per RT:
  SELECT w.nama_rt, COUNT(*) jml FROM (SELECT k.id, wi.nama AS nama_rt FROM keluargas k JOIN wilayahs wi ON wi.id = k.rt_id WHERE k.deleted_at IS NULL) x JOIN wargas wg ON wg.kk_id = x.id AND wg.deleted_at IS NULL AND wg.meninggal = 0 GROUP BY x.nama_rt
  (cara lebih sederhana: SELECT wi.nama, COUNT(wg.id) FROM wilayahs wi LEFT JOIN keluargas k ON k.rt_id = wi.id AND k.deleted_at IS NULL LEFT JOIN wargas wg ON wg.kk_id = k.id AND wg.deleted_at IS NULL AND wg.meninggal = 0 WHERE wi.tingkat = 'RT' AND wi.deleted_at IS NULL GROUP BY wi.id, wi.nama ORDER BY wi.nama)

• Komposisi jenis kelamin / umur:
  SELECT jenis_kelamin, COUNT(*) FROM wargas WHERE deleted_at IS NULL AND meninggal = 0 GROUP BY jenis_kelamin
  SELECT CASE WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 13 THEN '0-12' WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 18 THEN '13-17' WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 60 THEN '18-59' ELSE '60+' END kelompok, COUNT(*) FROM wargas WHERE deleted_at IS NULL AND meninggal = 0 GROUP BY kelompok

• Keluarga miskin per RT:
  SELECT wi.nama, COUNT(k.id) FROM keluargas k JOIN wilayahs wi ON wi.id = k.rt_id WHERE k.deleted_at IS NULL AND k.status_miskin = 'Miskin' GROUP BY wi.nama

• Tunggakan iuran per bulan:
  SELECT periode_bulan, COUNT(*) tagihan, SUM(nominal) tunggakan FROM iurans WHERE deleted_at IS NULL AND status = 'belum_bayar' GROUP BY periode_bulan ORDER BY periode_bulan

• Iuran terbayar per bulan (pemasukan iuran):
  SELECT i.periode_bulan, SUM(p.jumlah_bayar) diterima FROM pembayaran_iurans p JOIN iurans i ON i.id = p.iuran_id WHERE i.deleted_at IS NULL GROUP BY i.periode_bulan ORDER BY i.periode_bulan

• Saldo kas per unit:
  SELECT ku.nama, ku.jenis, SUM(CASE WHEN kt.tipe = 'masuk' THEN kt.jumlah ELSE -kt.jumlah END) saldo FROM kas_units ku JOIN kas_transaksis kt ON kt.kas_unit_id = ku.id WHERE ku.deleted_at IS NULL GROUP BY ku.id, ku.nama, ku.jenis ORDER BY saldo DESC

• Arus kas bulanan satu unit (contoh RT 02 RW 03):
  SELECT DATE_FORMAT(kt.tanggal, '%Y-%m') bulan, SUM(CASE WHEN kt.tipe='masuk' THEN kt.jumlah ELSE 0 END) masuk, SUM(CASE WHEN kt.tipe='keluar' THEN kt.jumlah ELSE 0 END) keluar FROM kas_transaksis kt JOIN kas_units ku ON ku.id = kt.kas_unit_id WHERE ku.nama LIKE '%RT 02 RW 03%' AND ku.deleted_at IS NULL GROUP BY bulan ORDER BY bulan

• Pengeluaran per kategori (semua unit atau satu unit):
  SELECT kategori, SUM(jumlah) total FROM kas_transaksis kt JOIN kas_units ku ON ku.id = kt.kas_unit_id WHERE kt.tipe = 'keluar' AND ku.deleted_at IS NULL GROUP BY kategori ORDER BY total DESC

• Warga terverifikasi:
  SELECT SUM(is_verified = 1) verified, SUM(is_verified = 0) belum FROM wargas WHERE deleted_at IS NULL

TANGGAL HARI INI: {$today} ({$isoToday} WIB).
KAMU SEDANG MEMBANTU: {$userName}
PROMPT;
    }
}
