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
  - **Alamat KTP (Input Manual Lengkap)**:
    - Alamat KK lengkap (jalan sampai negara) - sesuai dokumen KK
    - RT KTP, RW KTP, Kelurahan KTP, Kecamatan KTP, Kabupaten KTP, Provinsi KTP
  - **Alamat Domisili (Koneksi Sistem Wilayah)**:
    - Alamat domisili (input jalan saja)
    - RT ID (dropdown pilih dari sistem wilayah)
    - Auto-generate RT, RW, Kelurahan, Kecamatan, Kabupaten, Provinsi domisili dari rt_id
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
  - Iuran Kebersihan (Bulanan - Rp 25,000)
  - Iuran Keamanan (Bulanan - Rp 30,000)
  - Iuran Sosial/Kematian (Bulanan - Rp 10,000)
  - Iuran Kampung (Bulanan - Rp 10,000)
  - Iuran Acara 17 Agustus (Tahunan - Rp 50,000)
- **Pengelolaan Iuran**:
  - KK-Based Billing System (per KK bukan per warga)
  - Many-to-Many Connection (Keluarga ↔ Jenis Iuran)
  - Manual Tagihan Generation per periode (anti-duplicate)
  - Hierarchical Generation (RT hanya wilayahnya)
  - Input pembayaran (tunai, digital payment)
  - Riwayat pembayaran dengan audit trail
  - Tunggakan iuran tracking
- **Laporan Iuran**:
  - Statistik pembayaran per RT/RW/Kelurahan
  - Coverage rate tracking (lunas vs tunggakan)
  - Simple dashboard dengan grafik
  - Export laporan ke Excel/PDF
- **Special Cases**:
  - Discount system per KK (RT bisa set nominal_custom)
  - Free iuran untuk kasus khusus (janda/pensiunan)
  - Audit trail untuk perubahan nominal
- **Payment Methods**:
  - Cash (Tunai) dengan kuitansi
  - Digital payment (QRIS, E-wallet)
  - Tidak memerlukan bukti transfer (trust-based)

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
  golongan_darah, agama, status_perkawinan, pekerjaan, kewarganegaraan,
  pendidikan_terakhir, foto_ktp, kk_id, hubungan_keluarga, no_telepon, email,
  created_by, updated_by, created_at, updated_at, deleted_at}
  - **Alamat tidak duplikasi**: Data alamat diambil dari keluarga_id linkage
- **keluargas** - {id, no_kk, kepala_keluarga_id, alamat_kk, rt_kk, rw_kk, kelurahan_kk, kecamatan_kk,
  kabupaten_kk, provinsi_kk, alamat_domisili, rt_id, status_domisili_keluarga,
  tanggal_mulai_domisili_keluarga, keterangan_status, created_at, updated_at, deleted_at}
  - **Alamat KTP (Manual Input)**:
    - alamat_kk: Alamat lengkap sesuai KK (jalan sampai negara)
    - rt_kk, rw_kk, kelurahan_kk, kecamatan_kk, kabupaten_kk, provinsi_kk: Input manual sesuai KK
  - **Alamat Domisili (rt_id only - Dynamic Loading)**:
    - alamat_domisili: Alamat jalan saja untuk domisili
    - rt_id: Foreign key ke wilayahs.id (tingkat=RT) - hanya ini yang disimpan
    - rt, rw, kelurahan, kecamatan, kabupaten, provinsi domisili: Di-load dynamically via rt_id relationship
  - **Status Domisili enum**: ['Tetap', 'Non Domisili', 'Luar', 'Sementara']

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
- **keluargas** → **wilayahs** (melalui rt_id foreign key ke master data wilayah)
- **wilayahs** (hierarchical structure untuk RT/RW/Kelurahan)
  - Kelurahan → RW → RT (parent-child relationship)
- **keluargas** → **iurans** → **pembayaran_iurans**
- **pengaturan_sistems** (system configuration)

## Wilayah ↔ Keluarga Linking Strategy
- **Dual Address System**:
  - **Alamat KTP**: Input manual lengkap (sesuai dokumen KK)
    - alamat_kk: Textarea untuk alamat lengkap
    - rt_kk, rw_kk, kelurahan_kk, kecamatan_kk, kabupaten_kk, provinsi_kk: Input individual
  - **Alamat Domisili**: Koneksi sistem wilayah via rt_id
    - alamat_domisili: Input jalan saja
    - rt_id: Dropdown pilih RT dari 229 data yang tersedia
    - Dynamic loading: rt, rw, kelurahan, kecamatan, kabupaten, provinsi di-load otomatis via relationship
- **Single Source of Truth**: Hanya rt_id yang disimpan, data wilayah lain di-load dynamically
- **Data Integrity**: Foreign key constraint pada rt_id memastikan data wilayah valid
- **Storage Efficiency**: Hemat 200+ bytes per record dengan menghilangkan redundant data
- **Query Performance**: Hubungan efisien untuk reporting dan filtering domisili
- **Real-time Sync**: Perubahan data wilayah langsung refleksi ke semua keluarga
- **Flexibility**: Dukungan alamat KTP berbeda dengan domisili (status Non Domisili/Luar)
- **Status Domisili Logic**:
  - Tetap: Alamat KTP = Alamat Domisili
  - Non Domisili: Alamat KTP di sini, Domisili di luar
  - Luar: Alamat KTP di luar, Domisili di sini
  - Sementara: Kontrak/Ngontrak

## Key Design Changes (Updated Structure)
1. **Alamat & Status Domisili** dipindah dari warga ke keluarga level
2. **Dual Address System**: Alamat KTP (manual) vs Alamat Domisili (sistem wilayah)
3. **Soft Deletes** di semua tabel utama untuk data integrity
4. **Audit Trail** dengan created_by & updated_by fields
5. **Hierarchical Wilayah** dengan parent_id relationship
6. **Status Domisili** 4 jenis: Tetap, Non Domisili, Luar, Sementara
7. **Wilayah-Keluarga Linking**: rt_id foreign key dengan dynamic loading (hanya rt_id yang disimpan)
8. **Data Separation**: Alamat KTP input manual lengkap, Alamat Domisili konek sistem wilayah
9. **Single Source of Truth**: Warga tanpa duplikasi alamat, semua data alamat di keluarga level
10. **Storage Optimization**: Tidak ada redundant data domisili, hemat 200+ bytes per record
11. **Real-time Sync**: Perubahan data wilayah otomatis refleksi ke semua keluarga records

Total: **10 tabel** dengan optimasi performa dan foreign key constraints.

## Target Pengguna
- Administrasi Kelurahan
- Petugas Pelayanan Publik
- Kepala Kelurahan
- Sekretaris Kelurahan
- Warga Publik (untuk portal akses informasi)

## Prioritas Pengembangan (MVP - Minimum Viable Product) - **COMPLETED**
1. ✅ Manajemen Pengguna & Autentikasi dengan 4 role (Admin, Lurah, RW, RT)
2. ✅ Manajemen Data Warga dengan AJAX CRUD menggunakan modal popup
3. ✅ Dashboard sederhana dengan statistik dasar (Chart.js)
4. ✅ Template SB Admin 2 yang responsif dengan DataTables
5. ✅ Search dan filter data warga real-time
6. ✅ Manajemen Iuran dasar dengan tracking tunggakan
7. ✅ Portal Publik Warga dengan sensor data dan keamanan

## Remaining Development (Post-MVP)
1. Dashboard & Reporting (advanced analytics)
2. System Administration (backup/restore, settings)
3. Advanced reporting with export functionality

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

## Timeline Estimasi - **COMPLETED**
- ✅ **Week 1-2**: Setup project Laravel 12 + SB Admin 2, manajemen pengguna dengan 4 role
- ✅ **Week 3-4**: Manajemen data warga dan Keluarga dengan AJAX CRUD modal popup
- ✅ **Week 5-6**: Dashboard real-time dengan Chart.js, laporan dasar
- ✅ **Week 7-8**: Manajemen iuran lengkap dengan tracking tunggakan
- ✅ **Week 9-10**: Testing AJAX functionality, debugging, dan deployment
- ✅ **Week 11-12**: Portal Publik development dengan security & sanitization
- ✅ **Week 13**: Final testing, code cleanup, dan production deployment

## Future Development Timeline
- **Week 14-16**: Advanced dashboard & reporting system
- **Week 17-18**: System administration module (backup/restore, settings)
- **Week 19-20**: Performance optimization & security audit

## Status Current Implementation (Updated)

### ✅ **COMPLETED MODULES**:

#### 1. **Authentication & User Management**
- Multi-level authentication system (Admin, Lurah, RW, RT)
- Session management with "Ingat Saya" functionality
- User profile management
- Default account credentials removed from login page

#### 2. **Data Master & Wilayah Management**
- Hierarchical wilayah system (Kelurahan → RW → RT)
- Public API endpoints for wilayah data access
- Cascading dropdown system (Kelurahan → RW → RT)
- Real Bendul Merisi sample data (17 wilayah records)

#### 3. **Keluarga Management System**
- **KK-First Architecture**: Warga creation through keluarga module
- Dual address system:
  - **Alamat KTP**: Manual input (rt_kk, rw_kk, kelurahan_kk, kecamatan_kk, kabupaten_kk, provinsi_kk)
  - **Alamat Domisili**: Cascading dropdown with rt_id linkage
- Foto KK upload with validation and display
- CRUD operations with modal interface
- Activity logging system

#### 4. **Warga Management System**
- Edit-only module (new warga created via keluarga)
- Fundamental data focus (4 sections): Data Pribadi, Orang Tua, Kontak, Data Lainnya
- Foto KTP upload and management
- Improved search & filter with auto-trigger (500ms debounce)
- Enhanced table display with proper address resolution
- Action buttons with disabled states for unavailable features

#### 5. **Photo Storage System**
- **Standardized Structure**:
  ```
  storage/app/public/documents/
  ├── ktp/ktp_{nik}_{timestamp}.{ext}
  └── kk/kk_{no_kk}_{timestamp}.{ext}
  ```
- Improved file naming with traceable identifiers
- Consistent validation: `mimes:jpeg,jpg,png|max:2048`
- Automatic old file cleanup on updates
- Proper path resolution and asset handling

#### 6. **API & AJAX Implementation**
- Public API routes without authentication
- Real-time validation and feedback
- Modal-based CRUD operations
- Toast notifications and loading states
- Proper error handling and user feedback

### ✅ **COMPLETED MODULES**:

#### 5. Manajemen Iuran Warga (Community Fee Management) - FULLY IMPLEMENTED
- **Jenis Iuran Management**: Complete CRUD dengan soft deletes
- **Iuran Billing System**: KK-based billing generation with duplicate detection
- **Payment Processing**: Multiple payment methods (cash, transfer, qris, ewallet)
- **Bulk Generation**: Advanced generate system with preview functionality
- **Smart Payment Processing**: Auto-disable payment button for 'lunas' status
- **Enhanced Duplicate Detection**: Proper constraint violation handling
- **Payment History**: Complete audit trail dengan nomor referensi tracking
- **Advanced Filtering**: Active jenis iuran and keluarga_iuran connections filtering

### ✅ **COMPLETED MODULES**:

#### 6. Portal Publik Warga (Public Citizen Portal) - FULLY IMPLEMENTED
- **Complete Portal System**: 3 dedicated pages (iuran, warga, keluarga) with unified design
- **Advanced Security Features**:
  - Dynamic captcha system with auto-refresh after each submission
  - Rate limiting (5 requests/minute per IP) with proper error handling
  - IP tracking and comprehensive audit logging
- **Data Sanitization System**:
  - NIK/No KK: First 6 + 6 middle digits masked + last 4 visible
  - Names: First 4 characters + "***" for privacy
  - Email: Format like abc***@gmail***.com
  - Addresses: Partial masking for location privacy
  - Phone numbers: First 3 + "***" + last 3 digits
  - Birth dates: Day + month + "****" (year masked)
- **Enhanced User Experience**:
  - Toast notifications replacing alerts
  - Responsive SB Admin 2 design with proper CDN integration
  - Auto-form submission handling with loading states
  - Complete error handling with user-friendly messages
- **Data Display Features**:
  - Iuran: 12-month payment history with formatted summaries
  - Warga: Complete identity information with family relationships
  - Keluarga: Address information (KTP & domisili) with member list
  - Date formatting: Indonesian locale (d F Y format)
- **Security Monitoring**: AktivitasLog model tracks all public access attempts

### 🔄 **PLANNED MODULES**:

#### 1. **Dashboard & Reporting**
- Real-time statistics and monitoring
- Chart.js integration for data visualization
- Demographics reporting per wilayah
- Export functionality (PDF/Excel)

#### 2. **System Administration**
- Backup and restore functionality
- System settings management
- Activity log monitoring dashboard
- User activity tracking

## Technical Achievements

### **Database Optimization**
- Soft deletes implemented across all tables
- Proper foreign key constraints and relationships
- Activity logging with comprehensive audit trail
- Storage optimization with single source of truth principle

### **User Experience Improvements**
- Auto-trigger search functionality
- Disabled state indicators for unavailable actions
- Consistent validation feedback
- Smooth modal transitions and loading states

### **Security Enhancements**
- Removed default credentials from login page
- Rate limiting preparation for public access
- Input validation and sanitization
- Session management with configurable lifetime

### **Performance Optimizations**
- Efficient database queries with proper indexing
- Lazy loading for relationships
- Optimized file storage structure
- AJAX-based interactions for better responsiveness

---
*Dokumen ini akan terus diperbarui sesuai dengan perkembangan proyek.*