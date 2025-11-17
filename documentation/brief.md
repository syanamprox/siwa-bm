# Sistem Informasi Warga (SIWA) Kelurahan

## Deskripsi
Sistem Informasi Warga (SIWA) adalah aplikasi berbasis web yang dibanggunakan untuk mengelola data penduduk di tingkat kelurahan. Sistem ini menggunakan framework Laravel sebagai backend dan SB Admin 2 sebagai template frontend.

## Fitur-Fitur Dasar

### 1. Manajemen Pengguna (User Management)
- **Login & Logout**: Sistem autentikasi multi-level (tanpa registrasi publik)
- **Admin-Only User Creation**: Hanya administrator yang dapat membuat akun pengguna baru
- **Role Management**:
  - **Administrator (Admin)**: Kontrol penuh sistem, manajemen user, master data, backup & restore
  - **Lurah**: Akses ke semua data warga, laporan, validasi surat, approval
  - **RW**: Akses data warga per RW, pengelolaan iuran RW, laporan RW
  - **RT**: Akses data warga per RT, pengelolaan iuran RT, input data warga
- **User Profile**: Profil pengguna dengan kemampuan edit data diri (password, username)
- **Permission Control**: Hak akses berdasarkan hierarki wilayah (Kelurahan → RW → RT)

### 2. Manajemen Data Warga (Citizen Data Management)
- **Data KTP (Data Tetap)**:
  - Nomor Induk Kependudukan (NIK)
  - Nama Lengkap
  - Tempat & Tanggal Lahir
  - Jenis Kelamin
  - Alamat KTP (sesuai dokumen KTP)
  - Agama
  - Status Perkawinan
  - Pekerjaan
  - Kewarganegaraan
  - Pendidikan Terakhir
  - Foto KTP/Paspor
- **Data Domisili (Data Dinamis)**:
  - Alamat Domisili (tempat tinggal aktif)
  - RT, RW, Kelurahan domisili
  - Tanggal mulai domisili
  - Status domisili (tetap, kontrak, ngontrak)
  - No Telepon/HP aktif
  - Email aktif
- **Data Keluarga**:
  - Nomor Kartu Keluarga (KK)
  - Hubungan dalam Keluarga
- **CRUD Operations**: Create, Read, Update, Delete data warga
- **Search & Filter**: Pencarian berdasarkan KTP atau domisili
- **Histori Perpindahan**: Track perpindahan domisili warga

### 3. Manajemen Keluarga (Family Management)
- **Data KK (Data KTP)**:
  - Nomor KK
  - Kepala Keluarga
  - Alamat KK (sesuai dokumen KK)
  - Anggota Keluarga
- **Domisili KK (Data Dinamis)**:
  - Alamat domisili aktif KK
  - RT, RW, Kelurahan domisili
  - Tanggal mulai domisili
- **Hubungan Kepala Keluarga dengan Anggota**
- **Histori Perubahan Data KK**
- **Histori Perpindahan Domisili KK**

### 4. Dashboard & Laporan (Dashboard & Reports)
- **Dashboard Overview**:
  - Total jumlah warga per RT/RW
  - Statistik demografi (gender, umur, pendidikan)
  - Real-time monitoring iuran
  - Grafik pemasukan dan tunggakan iuran
- **Laporan**:
  - Laporan Demografi per wilayah
  - Laporan Kependudukan
  - Laporan Iuran (bulanan, tahunan)
  - Export ke PDF/Excel

### 5. Manajemen Iuran Warga (Community Fee Management)
- **Jenis Iuran**:
  - Iuran Kebersihan (Bulanan)
  - Iuran Keamanan (Satpam)
  - Iuran Sosial/Kematian
  - Iuran Infrastruktur
- **Pengelolaan Iuran**:
  - Pembuatan tagihan iuran per warga per RT/RW
  - Pembayaran iuran (tunai, transfer)
  - Riwayat pembayaran
  - Tunggakan iuran
- **Laporan Iuran**:
  - Laporan pembayaran bulanan per RT/RW
  - Daftar tunggakan per warga
  - Statistik pemasukan iuran
  - Export laporan ke PDF/Excel
- **Fitur Tambahan**:
  - Reminder otomatis untuk tunggakan
  - Kalkulasi denda keterlambatan
  - Dashboard monitoring iuran real-time

### 6. Portal Publik Warga (Public Citizen Portal)
- **Pencarian Data Warga**:
  - Search berdasarkan NIK atau nama lengkap
  - Verifikasi dengan captcha/keamanan
  - Data pribadi disensor (KTP, email, telepon)
  - Format output: Nama, RT/RW, status kependudukan
- **Cek Status Keluarga**:
  - Input nomor KK untuk melihat data keluarga
  - Informasi umum: jumlah anggota, kepala keluarga, alamat
  - Data sensitif disensor (tanggal lahir, nomor telepon, email)
- **Monitoring Iuran**:
  - Cek status pembayaran iuran per NIK
  - Informasi: bulan yang sudah dibayar, tunggakan, status
  - Nominal ditampilkan tanpa detail sensitif
  - QR code verification untuk keamanan
- **Keamanan Public Portal**:
  - Rate limiting untuk mencegah abuse
  - IP tracking untuk monitoring
  - Log aktivitas publik yang mencurigakan
- **Informasi Umum**:
  - Alur dan prosedur pembayaran iuran
  - Jadwal pembayaran bulanan
  - Kontak petugas RT/RW
  - Pengumuman kelurahan

### 7. Pengaturan Sistem (System Settings)
- **Pengaturan Umum**:
  - Identitas Kelurahan
  - Kepala Kelurahan
  - Sekretaris Kelurahan
- **Master Data**:
  - Data Agama
  - Data Pekerjaan
  - Data Pendidikan
  - Data Status Perkawinan
- **Backup & Restore**: Backup database dan restore

## Persyaratan Sistem (System Requirements)
- **Server Requirements**:
  - PHP 8.3 atau lebih tinggi
  - Web Server (Apache/Nginx)
  - MySQL 8.0+ atau MariaDB 10.3+
  - Composer 2.0+
  - PHP Extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD/ImageMagick
- **Client Requirements**:
  - Modern web browser (Chrome, Firefox, Safari, Edge)
  - Internet connection
  - Minimum resolution: 1024x768

## Teknologi yang Digunakan
- **Backend**: Laravel 12 (Latest Stable Version)
  - PHP 8.3+ minimum requirement
  - Composer dependency manager
  - Artisan CLI tools
  - API Resources untuk AJAX response
- **Frontend**: SB Admin 2 (Latest Version)
  - Bootstrap 5.3+ framework
  - jQuery 3.7+ untuk AJAX interactions
  - Font Awesome 6.0+ icons
  - Chart.js untuk dashboard
  - DataTables untuk tabel dinamis
- **Database**: MySQL 8.0+ / MariaDB 10.3+
- **Authentication**: Laravel Breeze ( dengan Blade dan Tailwind )
- **AJAX Implementation**:
  - Full AJAX CRUD operations dengan modal popup (tanpa page refresh)
  - Real-time validation dengan JavaScript
  - Smooth loading animations dengan Spin.js
  - Toast notifications untuk feedback
- **File Upload**: Laravel File Storage dengan AJAX upload
- **PDF Generation**: DomPDF
- **Excel Export**: Laravel Excel ( maatwebsite/excel )
- **Form Validation**: Laravel Form Request Validation + Client-side validation
- **Environment Management**: .env configuration

## Struktur Database (Tabel Utama)

### 1. Master Data Tables (2-7 kolom) - dengan Soft Deletes
- **jenis_iuran** - {id, nama, deskripsi, nominal_default, periode, status_aktif, created_at, updated_at, deleted_at}
- **wilayah** - {id, kode, nama, tingkat, parent_id, created_at, updated_at}
- **pengaturan_sistem** - {id, key, value, keterangan, created_at, updated_at}

### 2. User Management Tables (3-7 kolom) - dengan Soft Deletes
- **users** - {id, username, password, role, status_aktif, created_at, updated_at, deleted_at}
- **user_wilayah** - {id, user_id, wilayah_id, created_at}

### 3. Core Data Tables (8-22 kolom) - dengan Soft Deletes
- **warga** - {id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin,
  golongan_darah, alamat_ktp, rt_ktp, rw_ktp, kelurahan_ktp, kecamatan_ktp,
  kabupaten_ktp, provinsi_ktp, agama, status_perkawinan, pekerjaan, kewarganegaraan,
  pendidikan_terakhir, foto_ktp, kk_id, hubungan_keluarga, alamat_domisili,
  rt_domisili, rw_domisili, kelurahan_domisili, no_telepon, email, status_domisili,
  tanggal_mulai_domisili, created_at, updated_at, deleted_at, created_by, updated_by}
- **keluarga** - {id, no_kk, kepala_keluarga_id, alamat_kk, rt_kk, rw_kk,
  kelurahan_kk, created_at, updated_at, deleted_at}

### 4. Transaction Tables (5-12 kolom) - dengan Soft Deletes
- **iuran** - {id, warga_id, kk_id, jenis_iuran_id, rt_id, rw_id, nominal,
  periode_bulan, status, jatuh_tempo, denda_terlambatan, reminder_sent_at,
  created_at, updated_at, deleted_at}
- **pembayaran_iuran** - {id, iuran_id, jumlah_bayar, metode_pembayaran,
  bukti_pembayaran, tanggal_bayar, denda_dibayar, petugas_id, created_at, deleted_at}

### 5. History & Audit Tables (6-10 kolom)
- **aktivitas_log** - {id, user_id, tabel_referensi, id_referensi,
  jenis_aktivitas, deskripsi, data_lama, data_baru, created_at}
- **histori_perpindahan** - {id, warga_id, kk_id, jenis_perpindahan,
  rt_lama, rw_lama, kelurahan_lama, rt_baru, rw_baru, kelurahan_baru,
  tanggal_pindah, alasan_pindah, petugas_id, created_at}

## Relasi Utama
- **users** (admin/lurah/rw/rt) → **user_wilayah** → **wilayah** (RT/RW/Kelurahan)
- **warga** → **keluarga** (kk_id)
- **warga** + **jenis_iuran** → **iuran** → **pembayaran_iuran**
- **warga** → **histori_perpindahan** (track perpindahan domisili)
- **pengaturan_sistem** (system configuration)

## Views untuk Query Optimasi
- **v_warga_domisili** - Join warga dengan domisili aktif
- **v_laporan_iuran_wilayah** - Laporan iuran per RT/RW

Total: **8 tabel** dengan rata-rata **10 kolom** per tabel untuk performa optimal.

## Target Pengguna
- Administrasi Kelurahan
- Petugas Pelayanan Publik
- Kepala Kelurahan
- Sekretaris Kelurahan

## Prioritas Pengembangan (MVP - Minimum Viable Product)
1. Manajemen Pengguna & Autentikasi dengan 4 role (Admin, Lurah, RW, RT)
2. Manajemen Data Warga dengan AJAX CRUD menggunakan modal popup
3. Dashboard sederhana dengan statistik dasar (Chart.js)
4. Template SB Admin 2 yang responsif dengan DataTables
5. Search dan filter data warga real-time
6. Manajemen Iuran dasar dengan tracking tunggakan
7. Portal Publik Warga dengan sensor data dan keamanan

## Timeline Estimasi
- **Week 1-2**: Setup project Laravel 12 + SB Admin 2, manajemen pengguna dengan 4 role
- **Week 3-4**: Manajemen data warga dan Keluarga dengan AJAX CRUD modal popup
- **Week 5-6**: Dashboard real-time dengan Chart.js, laporan dasar
- **Week 7-8**: Manajemen iuran lengkap dengan tracking tunggakan
- **Week 9-10**: Testing AJAX functionality, debugging, dan deployment

---
*Dokumen ini akan terus diperbarui sesuai dengan perkembangan proyek.*