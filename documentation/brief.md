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
  - Golongan Darah
  - Alamat KTP (sesuai dokumen KTP)
  - Agama
  - Status Perkawinan
  - Pekerjaan
  - Kewarganegaraan
  - Pendidikan Terakhir
  - Foto KTP/Paspor
- **Data Kontak Personal**:
  - No Telepon/HP aktif
  - Email aktif
- **Data Relasi Keluarga**:
  - Hubungan dalam Keluarga (Kepala Keluarga, Istri, Anak, dll)
  - Referensi ke KK (keluarga_id)
- **CRUD Operations**: Create, Read, Update, Delete data warga
- **Search & Filter**: Pencarian berdasarkan NIK atau nama
- **Tracking**: Created by & Updated by user audit

### 3. Manajemen Keluarga (Family Management)
- **Data Kartu Keluarga**:
  - Nomor KK
  - Kepala Keluarga (referensi ke data warga)
  - Anggota Keluarga (relasi ke tabel warga)
- **Data Alamat Keluarga (Level Keluarga)**:
  - Alamat KK lengkap
  - RT, RW, Kelurahan domisili
  - Kecamatan, Kabupaten, Provinsi
- **Status Domisili Keluarga**:
  - Tetap (Alamat Sini, Domisili Sini)
  - Non Domisili (Alamat Sini, Domisili Luar)
  - Luar (Alamat Luar, Domisili Sini)
  - Sementara (Kontrak/Ngontrak)
  - Tanggal mulai domisili
  - Keterangan status
- **Histori Perubahan Data KK**
- **Relasi One-to-Many**: Satu keluarga memiliki banyak warga

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
  - Bootstrap 4.6+ framework
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

### 1. User Management Tables - dengan Soft Deletes
- **users** - {id, name, email, email_verified_at, password, role, avatar, remember_token, created_at, updated_at}
  - Role enum: ['admin', 'lurah', 'rw', 'rt']

### 2. Master Data Tables - dengan Soft Deletes
- **wilayahs** - {id, kode, nama, tingkat, parent_id, created_at, updated_at, deleted_at}
  - Tingkat enum: ['Kelurahan', 'RW', 'RT']
  - Hierarchical: Kelurahan → RW → RT
- **jenis_iurans** - {id, nama, deskripsi, jumlah, periode, status, created_at, updated_at, deleted_at}
  - Periode enum: ['bulanan', 'tahunan', 'sekali']
- **pengaturan_sistems** - {id, key, value, tipe, kategori, deskripsi, created_at, updated_at, deleted_at}

### 3. Core Data Tables - dengan Soft Deletes
- **wargas** - {id, nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin,
  golongan_darah, alamat_ktp, rt_ktp, rw_ktp, kelurahan_ktp, kecamatan_ktp,
  kabupaten_ktp, provinsi_ktp, agama, status_perkawinan, pekerjaan, kewarganegaraan,
  pendidikan_terakhir, foto_ktp, kk_id, hubungan_keluarga, no_telepon, email,
  created_by, updated_by, created_at, updated_at, deleted_at}
- **keluargas** - {id, no_kk, kepala_keluarga_id, alamat_kk, rt_kk, rw_kk,
  kelurahan_kk, kecamatan_kk, kabupaten_kk, provinsi_kk, status_domisili_keluarga,
  tanggal_mulai_domisili_keluarga, keterangan_status, created_at, updated_at, deleted_at}
  - Status Domisili enum: ['Tetap', 'Non Domisili', 'Luar', 'Sementara']

### 4. Transaction Tables - dengan Soft Deletes
- **iurans** - {id, keluarga_id, jenis_iuran_id, jumlah, status, tanggal_jatuh_tempo,
  keterangan, created_at, updated_at, deleted_at}
- **pembayaran_iurans** - {id, iuran_id, warga_id, jumlah_bayar, tanggal_bayar,
  metode_pembayaran, bukti_pembayaran, keterangan, created_by,
  created_at, updated_at, deleted_at}

### 5. System Tables
- **aktivitas_logs** - {id, user_id, action, module, description, old_data,
  new_data, ip_address, user_agent, created_at, updated_at}
- **jobs** - Laravel queue jobs
- **cache** - Laravel cache

## Relasi Utama
- **keluargas** → **wargas** (satu keluarga memiliki banyak warga)
- **wargas** → **keluargas** (setiap warga terhubung ke satu keluarga)
- **keluargas** → **iurans** → **pembayaran_iurans**
- **wilayahs** (hierarchical structure untuk RT/RW/Kelurahan)
- **pengaturan_sistems** (system configuration)

## Key Design Changes (Updated Structure)
1. **Alamat & Status Domisili** dipindah dari warga ke keluarga level
2. **Soft Deletes** di semua tabel utama untuk data integrity
3. **Audit Trail** dengan created_by & updated_by fields
4. **Hierarchical Wilayah** dengan parent_id relationship
5. **Status Domisili** 4 jenis: Tetap, Non Domisili, Luar, Sementara

Total: **10 tabel** dengan optimasi performa dan foreign key constraints.

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

## Data Sample (Bendul Merisi - Surabaya)
- **Lurah**: RULLY PRASETYA NEGARA, S.STP.,M.Si
- **RW III**: BAMBANG SETYAWAN
- **RT I RW III**: TRI BAGUS WAHYUDI
- **RT II RW III**: AKHMAD SURYADI
- **RT III RW III**: M. YASIN
- **RT IV RW III**: SULICHAH

### Sample Data Structure:
- **5 Keluarga** dengan berbagai status domisili (Tetap, Non Domisili, Luar, Sementara)
- **5 Warga** dengan data KTP lengkap dan relasi keluarga
- **17 Wilayah** (1 Kelurahan, 12 RW, 4 RT di RW III)
- **6 Jenis Iuran** (Kebersihan, Keamanan, Pembangunan, dll)
- **12 Pengaturan Sistem** default

## Timeline Estimasi
- **Week 1-2**: Setup project Laravel 12 + SB Admin 2, manajemen pengguna dengan 4 role
- **Week 3-4**: Manajemen data warga dan Keluarga dengan AJAX CRUD modal popup
- **Week 5-6**: Dashboard real-time dengan Chart.js, laporan dasar
- **Week 7-8**: Manajemen iuran lengkap dengan tracking tunggakan
- **Week 9-10**: Testing AJAX functionality, debugging, dan deployment

## Status Current Implementation
✅ **COMPLETED**: Database structure rebuilt with updated design
- Alamat & status domisili moved to keluarga level
- Proper foreign key relationships established
- Real Bendul Merisi data seeded
- Migration & seeder system complete
- Ready for CRUD implementation

---
*Dokumen ini akan terus diperbarui sesuai dengan perkembangan proyek.*