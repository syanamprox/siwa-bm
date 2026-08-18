# SIWA — Frontend Rework ke Next.js

> **Technical Specification** untuk migrasi UI SIWA dari Laravel Blade + SB Admin 2 ke Next.js + Tailwind CSS, dengan design language & pattern dari template `d360_demo_businessplus`.

**Versi**: 1.0
**Tanggal**: 2026-08-18
**Status**: CONFIRMED — semua keputusan utama sudah dikonfirmasi user
**Penulis**: System Analyst

---

## 1. Executive Summary

SIWA (Sistem Informasi Warga Kelurahan) saat ini adalah Laravel 12 monolith dengan Blade + SB Admin 2 (Bootstrap 4.6.2 via CDN) + jQuery AJAX — 121 routes, ~15 views admin dengan 256–1.618 baris inline JS per view, tanpa component reuse, dengan risiko XSS (unescaped innerHTML) dan test coverage nol.

Rework ini membangun ulang **seluruh frontend** (admin + portal publik) dengan **Next.js 16 + Tailwind v4 + TanStack Query + Zustand**, mengadopsi design system premium dan pola arsitektur yang sudah terbukti dari `d360_demo_businessplus` (monorepo `frontend/` + `backend/`, Sanctum cookie auth, hooks per-modul, MODULES registry). **Backend Laravel + MySQL tetap utuh** — hanya API layer yang direstrukturisasi ke `routes/api.php` + Sanctum.

---

## 2. Business Context

### 2.1 Masalah dengan Frontend Saat Ini
1. **Maintenance nightmare**: 9 view admin berisi 256–1.618 baris inline JS dalam 1 block `<script>` — tidak bisa di-lint, di-reuse, atau di-cache
2. **XSS risk**: DOM di-rebuild via unescaped template literal `${data.nama}` → innerHTML di semua view
3. **Supply-chain risk**: Bootstrap/Chart.js/FontAwesome dari CDN tanpa SRI (hanya jQuery yang punya integrity)
4. **Bloat**: `public/vendor_sbadmin2/` 22MB (1.832 file) ter-commit tapi tidak dipakai
5. **Test coverage nol**: 25 test = 100% scaffolding Breeze, zero business logic yang teruji
6. **Visual**: SB Admin 2 terlihat jadul untuk standar 2026

### 2.2 Tujuan
- UI premium ala SaaS dashboard (soft card, sliding indicators, big KPI numbers) — konsisten dengan produk Digital360 lainnya (Business+ ERP)
- SPA navigation tanpa full reload, optimistic update
- Komponen reusable + TypeScript safety
- Semua modul SIWA dan portal publik pindah — **Blade views dihapus** setelah selesai (full replacement, no coexist)

### 2.3 KPI Sukses
- Semua 121 route ter-cover oleh endpoint API + halaman Next.js yang setara fungsinya
- Zero regression business logic (scoping wilayah per-role, validasi, activity log tetap jalan)
- Tidak ada lagi CDN tanpa SRI / inline JS raksasa
- Bundle size wajar (< 300KB gz untuk route awal)

---

## 3. Keputusan yang Sudah Dikonfirmasi

| # | Keputusan | Pilihan |
|---|---|---|
| D1 | Lokasi rework | **Rework in-place `siwa-bm`** — Laravel pindah ke `backend/`, Next.js di `frontend/`, history git tetap |
| D2 | Portal publik | **Next.js public pages** (route group `(portal)`, tanpa AppShell, tanpa auth) |
| D3 | Auth | **Email + password** via Sanctum cookie (field `email` sudah ada di users table + unique) |
| D4 | Scope | **Full rework semua modul** — eksekusi bertahap, tapi tidak ada modul yang dibiarkan di Blade |
| D5 | Design system | **Exact copy dari template** `d360_demo_businessplus` (brand #2563eb, Inter, tokens di `@theme`) |
| D6 | Stack | Next.js 16 App Router, React 19, TS, Tailwind v4 CSS-first, TanStack Query 5, Zustand 5, lucide-react, recharts, sonner |

---

## 4. Arsitektur Target

### 4.1 Struktur Monorepo

```
siwa-bm/                              # repo tetap siwa-bm
├── frontend/                         # Next.js app (BARU)
│   ├── src/
│   │   ├── app/
│   │   │   ├── (app)/                # Terautentikasi (AppShell: sidebar rail + topbar)
│   │   │   │   ├── layout.tsx
│   │   │   │   ├── page.tsx          # Dashboard (role-aware)
│   │   │   │   ├── warga/
│   │   │   │   ├── keluarga/
│   │   │   │   ├── wilayah/
│   │   │   │   ├── iuran/            # + jenis/, generate/, keluarga-iuran/
│   │   │   │   ├── laporan/
│   │   │   │   ├── users/
│   │   │   │   ├── pengaturan/
│   │   │   │   ├── backup/
│   │   │   │   └── changelog/
│   │   │   ├── (portal)/             # Publik, TANPA auth (tanpa AppShell)
│   │   │   │   ├── layout.tsx        # Layout portal sendiri (navbar portal)
│   │   │   │   ├── page.tsx          # Landing portal (/portal)
│   │   │   │   ├── cek-warga/
│   │   │   │   ├── cek-keluarga/
│   │   │   │   └── cek-iuran/
│   │   │   ├── login/
│   │   │   │   └── page.tsx          # Split-screen (copy template)
│   │   │   ├── layout.tsx            # Root: Inter font + providers
│   │   │   └── globals.css           # @theme tokens (copy template)
│   │   ├── components/
│   │   │   ├── ui/                   # primitives.tsx, Modal, Select (copy template)
│   │   │   ├── layout/               # AppShell, Sidebar, ModuleNav, Topbar (copy + adapt)
│   │   │   ├── KpiCard.tsx, PageHeader.tsx
│   │   │   └── [module]/             # warga/, keluarga/, iuran/, ...
│   │   ├── lib/                      # api-client.ts (copy), utils, constants, format
│   │   ├── hooks/                    # use-auth, use-warga, use-keluarga, use-iuran, ...
│   │   ├── stores/                   # auth-store.ts (adapt role SIWA)
│   │   ├── types/                    # mirror model SIWA
│   │   └── providers/
│   ├── .env.example                  # NEXT_PUBLIC_API_URL=http://localhost:8000/api
│   ├── package.json
│   └── next.config.ts
│
├── backend/                          # Laravel (DIPINDAH dari root)
│   ├── app/Http/Controllers/Api/     # BARU: Auth, Warga, Keluarga, Iuran, ...
│   ├── app/Http/Resources/           # BARU: JSON resources
│   ├── routes/api.php                # BARU: semua RESTful endpoints
│   ├── config/{sanctum,cors}.php     # UPDATE
│   └── ...                           # models, migrations TIDAK berubah
│
├── docs/                             # spec ini + handoff
└── README.md
```

### 4.2 Alur Request

```
Browser → Next.js (frontend:9910)
            ├─ (app) group → fetch http://backend:8000/api/* (Sanctum cookie, credentials:include)
            └─ (portal) group → fetch http://backend:8000/api/portal/* (public + rate-limited)
                      ↓
         Laravel API (thin controllers + existing models/services)
                      ↓
                   MySQL siwa
```

### 4.3 Port & Env
- Frontend: `PORT=9910` (hindari clash: businessplus 9906, dentwow 3002)
- `frontend/.env`: `NEXT_PUBLIC_API_URL=http://localhost:8000/api`
- `backend/.env`: tambah `FRONTEND_URL=http://localhost:9910`, `SANCTUM_STATEFUL_DOMAINS=localhost:9910`

---

## 5. Design System

**Source of truth**: `/home/syanampro/Projects/d360_demo_businessplus/src/` — copy exact, jangan estimasi.

### 5.1 Token (frontend/src/app/globals.css)
- Brand blue `#2563eb` (50–900 scale), canvas `#f4f6f8`, surface `#fff`, line `#eceff3`
- Success `#10b981`, warning `#f59e0b`, danger `#f43f5e`
- Font Inter, radius 0.875/1/1.375rem, shadow card/hover/pop
- Animasi: fade-up, slide-in-right, fade-in, modal-in, bump-in, shimmer (timing exact dari template)

### 5.2 Komponen (copy dari template)
`primitives.tsx` (Button 6 variant, Card, Input/Select/Textarea, Label, StatusBadge + Badge, Spinner, EmptyState, Loading, Skeleton, Avatar, Toggle), `Modal.tsx`, `Select.tsx`, `KpiCard.tsx`, `PageHeader.tsx`.

**Catatan StatusBadge**: template sudah punya mapping `lunas`/`belum lunas` — langsung terpakai untuk iuran. Tambahkan mapping status SIWA: `Aktif`, `Pindah`, `Tetap`, `Pendatang`, `Menunggu`, `Dikonfirmasi`.

### 5.3 Layout Shell
- **Sidebar rail 104px** dengan sliding gradient indicator (signature template) — item: Dashboard, Warga, Keluarga, Iuran, Wilayah, Laporan, Admin (gantikan MODULES ERP)
- **ModuleNav** (second-level) tampil di dalam halaman modul untuk sub-nav (mis. Iuran → Tagihan / Jenis Iuran / Generate / Konfigurasi KK)
- **Topbar**: sticky, blur, judul halaman + user info
- Role gating nav: RT hanya lihat Dashboard/Warga/Keluarga/Iuran (tanpa Wilayah/Users/Backup/Pengaturan)

---

## 6. Data Model — TIDAK BERUBAH

Semua 20 migrations, 11 models, relasi, dan business logic tetap. Yang berubah hanya cara data di-expose (Blade → JSON API).

Entity utama (existing): User (role: admin|lurah|rw|rt), UserWilayah (scoping), Wilayah (Kelurahan→RW→RT, self-referencing parent_id), Warga, Keluarga, JenisIuran, KeluargaIuran (pivot KK↔jenis), Iuran (tagihan), PembayaranIuran, AktivitasLog, PengaturanSistem.

### 6.1 Response Contract Standar (BARU)

```ts
// List (paginated)
{ data: T[], meta: { current_page, last_page, per_page, total } }
// Single
{ data: T }
// Error (Laravel default, dipertahankan)
{ message: string, errors?: Record<string, string[]> }
```

Semua endpoint baru mengikuti contract ini. Response ad-hoc controller lama dinormalisasi via API Resource.

---

## 7. API Design (routes/api.php)

### 7.1 Auth
```
GET  /sanctum/csrf-cookie          ( Sanctum bawaan)
POST /api/login                    { email, password } → set cookie
GET  /api/me                       → { id, name, email, role, avatar, wilayah: {rt_id, rw_id, kelurahan_id} }
POST /api/logout
```

### 7.2 Role Middleware (map dari middleware web existing)
`role:admin` (AdminMiddleware), `role:admin,lurah` (LurahMiddleware), `role:admin,lurah,rw` (RwMiddleware), `auth` semua role. Daftarkan alias di `bootstrap/app.php` → tetap pakai logic scoping yang sudah ada di controller.

### 7.3 Endpoint per Modul

| Grup | Endpoint (dari 121 routes existing, dinormalisasi) |
|---|---|
| **Dashboard** | `GET /api/dashboard` — stats role-aware (total warga/KK/iuran/tunggakan + tren 12 bulan + aktivitas terbaru) |
| **Warga** | CRUD `/api/warga` + `/statistics`, `/export`, `/import` (hapus stub, impelementasi nyata) |
| **Keluarga** | CRUD `/api/keluarga` + `/statistics`, `/wilayah-tree`, `/rt-info`, `/{id}/members` CRUD (add/remove member), `/import`, `/download-template`, `PATCH /{id}/status` |
| **Wilayah** | CRUD `/api/wilayah` + `/tree`, `/children/{parentId}` — admin only |
| **Jenis Iuran** | CRUD `/api/jenis-iuran` + `PUT /{id}/toggle-status` |
| **Keluarga Iuran** | `GET /api/keluarga-iuran/overview`, `/{keluargaId}/available`, `/{keluargaId}/active`, POST/PUT/DELETE connect |
| **Iuran** | `GET /api/iuran` (filter: rt, bulan, status, jenis) + `/statistics`, `/{id}/payment-history`, `POST /{id}/bayar` (cash/QRIS/e-wallet + bukti upload) |
| **Generate** | `GET /api/iuran/generation/rt-options`, `/preview`, `POST /generate` (dry-run preview dulu) |
| **Laporan** | `GET /api/laporan/wilayah`, `/kependudukan`, `GET /laporan/export?type=...` (Excel/PDF stream) |
| **Users** | CRUD `/api/users` + `/toggle-status`, `/reset-password` — admin only |
| **Pengaturan** | `GET/PUT /api/pengaturan` (grouped) |
| **Backup** | `GET /api/backup`, `POST /create`, `GET /download/{filename}`, `DELETE /{filename}`, `POST /restore`, `GET /status` — admin only |
| **Aktivitas** | `GET /api/aktivitas` (audit trail, admin+lurah) |
| **Portal** (public) | `POST /api/portal/cek-warga`, `/cek-keluarga`, `/cek-iuran` — throttle 5/min, sanitasi NIK tetap server-side |

### 7.4 Aturan Penting
- **Scoping wilayah TIDAK BOLEH hilang**: RT hanya melihat data warga/keluarga/iuran di RT-nya (logic existing di controller dipertahankan, dipindah ke API controller + query scope)
- **Sanitasi portal tetap server-side** (format NIK `3578******0010`) — frontend hanya render
- **Activity log**: semua mutasi tulis AktivitasLog (helper existing)
- Response JSON semua (middleware `EnsureJsonRequest` → 401/403/422 JSON, bukan HTML redirect)

---

## 8. User Stories (MoSCoW)

### Must Have
```
Sebagai user terautentikasi, saya login dengan email+password di halaman premium,
sehingga sesi aman (Sanctum) dan first impression bagus.

Sebagai Admin/Lurah/RW/RT, saya melihat dashboard role-aware dengan KPI cards
(total warga, keluarga, tagihan bulan ini, tunggakan) + chart tren,
sehingga kondisi wilayah terbaca cepat.

Sebagai operator, saya CRUD Warga & Keluarga dengan form validasi + optimistic update,
sehingga input data cepat tanpa page reload.

Sebagai operator, saya mengelola anggota keluarga (tambah/pindah/hapus)
dengan popup dari halaman keluarga.

Sebagai Bendahara, saya generate tagihan iuran per RT dengan preview dry-run
sebelum commit, sehingga tidak salah generate.

Sebagai Bendahara, saya mencatat pembayaran (kas/QRIS/e-wallet) + upload bukti,
dan status lunas update tanpa reload.

Sebagai Warga, saya mengecek status iuran saya di portal publik
(masukkan no KK → 12 bulan ringkas) tanpa login, data sensitif tersensor.

Sebagai Admin, saya manage user + wilayah + backup + pengaturan.
```

### Should Have
```
Import/export Excel warga & keluarga (real implementation, hapus stub).
Laporan wilayah & kependudukan dengan export Excel/PDF.
Filter + search server-side di semua list (debounced).
Aktivitas log viewer.
```

### Could Have
```
Dark mode toggle. PWA. Notifikasi WhatsApp tunggakan (pernah disebut di docs lama).
```

### Won't Have (rework ini)
```
Multi-tenant kelurahan ganda. Mobile native app. WebSocket real-time
(pakai TanStack revalidate dulu). Captcha portal (sudah dihapus by design, rate-limit cukup).
```

---

## 9. Feature Specifications (kritis saja)

### 9.1 Auth & Session
**Flow**: login → `getCsrfToken()` → `POST /api/login` → `GET /api/me` (hydrate Zustand) → redirect `/`. 401 → redirect `/login` + toast. Guard: `(app)` layout cek `initialized && user`, else redirect.
**Edge**: user `status_aktif=0` → 403 dengan pesan "Akun dinonaktifkan".

### 9.2 Dashboard Role-Aware
Admin/Lurah: seluruh kelurahan. RW: semua RT di RW-nya. RT: RT-nya saja. Satu endpoint `/api/dashboard` yang resolve scope server-side dari UserWilayah + role.

### 9.3 Generate Iuran (fitur paling risky)
**Flow**: 1) pilih RT + bulan + jenis → `GET /preview` (hitung KK kena tagihan, skip yang sudah ada, tampilkan total) → 2) konfirmasi → `POST /generate` (idempotent: skip existing tagihan bulan+jenis sama). UI pakai Stepper (pilih → preview → hasil).

### 9.4 Pembayaran
`POST /api/iuran/{id}/bayar`: validasi nominal vs tagihan (pelunasan/cicilan sesuai logic existing), upload bukti (multipart), create PembayaranIuran, update status iuran, tulis AktivitasLog. UI: modal bayar dengan preview jumlah + dropzone bukti.

### 9.5 Portal Publik
3 halaman + 1 landing. Rate limit 5/min/IP (throttle Laravel). Response tetap tersanitasi server-side. Next.js render hasil — tanpa cache (no-store) karena data pribadi.

---

## 10. Roadmap Eksekusi

| Phase | Isi | Estimasi |
|---|---|---|
| **P0 Foundation** | Restructure monorepo (Laravel → `backend/`), scaffold Next.js di `frontend/`, copy design system + api-client + auth, Sanctum + CORS, login + AppShell + dashboard kosong | 3–5 hari |
| **P1 Kependudukan** | Dashboard penuh + Warga + Keluarga (CRUD + members + import/export) | 5–7 hari |
| **P2 Iuran** | Jenis Iuran + KeluargaIuran + Generate (preview flow) + Tagihan + Pembayaran + tunggakan | 5–7 hari |
| **P3 Admin & Laporan** | Wilayah + Users + Pengaturan + Backup + Laporan (export) + Aktivitas log + Changelog | 4–6 hari |
| **P4 Portal + Cleanup** | Portal publik 3 cek + landing, hapus SEMUA Blade views + SB Admin 2 assets + vendor_sbadmin2 (22MB) + cookies.txt junk, update README | 3–4 hari |

Total: ~4–5 minggu eksekusi penuh.

---

## 11. Technical Decision Log

### TD-01: Next.js vs tetap Blade+alpine
**Keputusan**: Next.js. **Alasan**: user instruction + template sudah ada + pola proven di Business+ ERP; menyelesaikan masalah inline-JS, XSS, CDN-SRI, dan reusability sekaligus.

### TD-02: Sanctum vs session web cookie existing
**Keputusan**: Sanctum stateful cookie. **Alasan**: cross-origin (9910→8000) butuh CSRF + credential yang benar; pattern api-client template sudah handle (XSRF-TOKEN header). Endpoint `/admin/api/*` lama (session-based) tetap hidup selama transisi, dihapus di P4.

### TD-03: Response contract
**Keputusan**: normalisasi `{ data, meta }` via API Resource. **Alasan**: response ad-hoc lama tidak konsisten (campur HTML fragment di beberapa endpoint popup — semua itu dihapus).

### TD-04: Route popup legacy (`*-popup` routes)
**Keputusan**: DROP. **Alasan**: duplikat CRUD untuk versi popup lama — Next.js punya modal sendiri; 9 route bisa dihapus.

### TD-05: Backup feature
**Keputusan**: tetap server-side file backup (spatie-like existing logic), Next.js hanya UI list/create/download/restore. **Alasan**: logic backup tidak boleh ada di client.

### TD-06: Changelog page
**Keputusan**: pindah apa adanya (read git log via API existing). Bukan prioritas — masuk P3.

---

## 12. Risks & Mitigations

| Risk | Mitigation |
|---|---|
| Scoping wilayah bocor di API baru | Query scope test per role (unit test WargaPolicy) — tulis test SEBELUM porting |
| Session/CSRF cross-origin fail | Ikuti pattern template persis ( sudah proven di Business+ ) |
| Import Excel regression | Port SimpleImportService tanpa ubah logic, test dengan file template |
| Scope creep saat rework | Lock per phase; fitur baru → backlog pasca-rework |
| Dual-stack bingung selama transisi | `/admin/*` Blade tetap jalan sampai P4; jangan hapus sebelum modul setara selesai |

---

## 13. Open Questions (minor, tidak blocking P0)

1. **Port produksi & deploy**: frontend Next.js jalan di mana saat produksi? (pm2 di VPS yang sama dengan Laravel? subdomain `app.siwa.xxx` vs `siwa.xxx`?) — jawab sebelum P4.
2. **Changelog**: masih perlu? (halaman ini read git log — agak technical untuk user kelurahan)
3. **QRIS**: fitur QRIS di pembayaran — apakah ada gateway nyata atau hanya catatan metode? (existing: enum metode saja)
4. **Upload avatar warga/foto KK**: retensi existing path `public/uploads/` atau rapihkan ke `storage/app/public`? (rekomendasi: rapihkan saat porting upload controller)

---

**End of Specification**
