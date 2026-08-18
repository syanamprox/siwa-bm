# SIWA Next.js Rework — Progress Log

> Status eksekusi real-time dari spec `SPEC-REWORK-NEXTJS.md`.
> Branch: `feat/nextjs-rework` · Diperbarui: 2026-08-18

---

## Ringkasan Status

| Phase | Scope | Status |
|---|---|---|
| **P0** Foundation | Monorepo, Sanctum auth, design system, login, AppShell | ✅ **DONE** (committed) |
| **P0.5** Backend API | 15 API controller semua modul + scoping | ✅ **DONE** (committed) |
| **P1** Kependudukan FE | Dashboard KPI, CRUD Warga, CRUD Keluarga | ⏳ Next |
| **P2** Iuran FE | Tagihan, bayar, generate, jenis, konfigurasi KK | ⏳ |
| **P3** Admin & Laporan FE | Wilayah, users, pengaturan, backup, laporan | ⏳ |
| **P4** Portal + Cleanup | Portal publik FE, hapus Blade + junk | ⏳ |

---

## Commit Log (feat/nextjs-rework)

| Commit | Isi |
|---|---|
| `c0b03e4` | refactor: monorepo restructure — Laravel ke `backend/`, hapus cookies*.txt, root .gitignore |
| `8083fc4` | feat(backend): Sanctum username auth + REST API semua modul SIWA |
| `9f71026` | feat(frontend): Next.js 16 scaffold di root — design system + auth + placeholder |
| `01b7a63` | docs: spec teknis rework |

Base: `a20084a` (main).

---

## Yang Sudah Selesai

### 1. Monorepo Structure

```
siwa-bm/
├── backend/          # Laravel 12 (pindah dari root, semua models & DB utuh)
├── src/              # Next.js 16 (App Router, TS)
├── package.json      # Next.js di root
└── documentation/
```

### 2. Auth — Login USERNAME (bukan email)

- **Keputusan user**: petugas saja yang login (admin/lurah/rw/rt), identifier = username. Warga tidak login.
- Migration: `users.username` (unique, nullable), `email` jadi nullable.
- Seeder idempotent: `admin/admin123`, `lurah/lurah123`, `rw03/rw123`, `rt01–rt04/rt123`.
- `Api/AuthController`: login → `auth()->login()` + session regenerate (Sanctum stateful). `/api/me` return wilayah scope (rt_id/rw_id/kelurahan) untuk gating frontend.
- E2E curl test **5/5 PASS**: csrf-cookie → login → me → logout → wrong-password-422. CORS preflight 9910→8000 PASS.

### 3. Backend API — 15 Controller

Lokasi: `backend/app/Http/Controllers/Api/`

| Controller | Endpoint utama |
|---|---|
| AuthController | login/logout/me |
| DashboardController | GET /dashboard (role-aware stats + tren 6 bln + aktivitas) |
| WargaController | CRUD + statistics (scoped) |
| KeluargaController | CRUD + members add/remove + status + statistics |
| KeluargaIuranController | index / available / store / update / destroy |
| IuranController | index + statistics + **bayar** + payments riwayat |
| IuranGenerationController | rt-options + **preview dry-run** + generate (txn, idempotent) |
| JenisIuranController | CRUD + toggle-status |
| WilayahController | tree + children + CRUD (admin) |
| UserController | CRUD + toggle-status + reset-password |
| PengaturanController | index (grouped) + update |
| BackupController | list + create (mysqldump+zip) + download + restore |
| AktivitasController | index (admin+lurah) |
| PortalController | cek-warga/keluarga/iuran — public, rate-limit 5/min, sanitasi server-side |
| LaporanController | kependudukan + wilayah (scoped) |

**Traits** (`Api/Concerns/`):
- `ScopesToWilayah` — scoping per-role: admin/lurah = semua; rw = semua RT di RW-nya; rt = RT miliknya. Via `keluargas.rt_id`. Detail record di luar scope → 404 (tidak bocor keberadaan). **Ini improvement besar — controller lama TIDAK punya scoping sama sekali.**
- `LogsActivity` — audit pakai kolom REAL aktivitas_logs (helper lama menulis kolom phantom yang tidak ada di migration).

**Bug lama yang di-fix saat porting:**
- `PembayaranIuran` fillable salah → `created_by`/`keterangan`/`nomor_referensi` silently dropped saat create pembayaran. Sekarang benar.
- `Iuran::total_pembayaran` sum kolom `jumlah` yang tidak ada → sekarang `jumlah_bayar`.
- Wilayah `tree()` pakai lowercase 'kelurahan' → fix 'Kelurahan'.

**Response contract baru (konsisten semua endpoint):**
```json
// list
{ "data": [...], "meta": { "current_page", "last_page", "per_page", "total" } }
// single
{ "data": {...} }
// error (Laravel default)
{ "message": "..." }
```

### 4. Frontend Scaffold (P0)

- Copy exact design system dari template `d360_demo_businessplus`: token `@theme` (brand #2563eb, Inter), primitives (Button/Card/Input/StatusBadge/Avatar/Toggle/Skeleton), Modal, Select, KpiCard, AppShell.
- `constants.ts` — MODULES registry (Warga/Iuran/Wilayah/Laporan/Admin) dengan `roles[]` → sidebar otomatis filter per role (RT lihat 2 modul + Home, admin lihat semua).
- Login page split-screen SIWA (username + password).
- Dashboard placeholder greeter role-aware (implementasi penuh di P1).
- `/portal` placeholder (aktif P4).
- Build PASS: 4 static routes. Dev: `npm run dev` port 9910.

### 5. Cleanup

- ✅ Hapus `cookies*.txt` (4 file session junk yang ter-commit).
- ⏳ Belum: hapus `backend/resources/views/` (Blade), `public/vendor_sbadmin2` 22MB, dead routes `web.php` — di **P4** setelah semua modul FE selesai.

---

## Dev Environment

```bash
# Backend (Laravel) — dari backend/
cd backend && setsid nohup php artisan serve --port=8000 > /tmp/opencode/siwa_serve.log 2>&1 &
# DB: mysql siwa (root), .env sudah configured + FRONTEND_URL=http://localhost:9910

# Frontend (Next.js) — dari root
npm run dev   # port 9910

# Login: admin / admin123
```

Smoke test script: `/tmp/opencode/test_api.sh` (13 endpoint).

---

## Next: P1 — Kependudukan Frontend

1. `use-warga.ts`, `use-keluarga.ts` (TanStack Query hooks + optimistic update)
2. `/warga` — Table + filter + search + modal CRUD + foto KTP upload
3. `/keluarga` — Table + detail drawer (anggota, alamat dual) + modal CRUD + add/remove member + ganti kepala
4. Dashboard penuh — KPI cards (recharts) per role + recent activities + (RT/RW) pending iuran
5. Ganti dashboard placeholder dengan implementasi real

Setelah P1 → P2 Iuran (tagihan list + modal bayar + generate wizard preview) → P3 Admin → P4 Portal + Blade cleanup total.
