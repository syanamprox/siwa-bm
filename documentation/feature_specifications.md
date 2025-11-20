# Sistem Informasi Warga (SIWA) - Spesifikasi Fitur Detail

## Overview
Dokumen ini berisi spesifikasi detail fitur-fitur yang akan diimplementasikan dalam Sistem Informasi Warga (SIWA) Kelurahan berdasarkan brief dan struktur database yang telah dirancang.

## Current Implementation Status

### ✅ **COMPLETED FEATURES**

#### Authentication & User Management
- **Multi-level Login System**: 4 role hierarchy (Admin, Lurah, RW, RT)
- **Session Management**:
  - Standard Laravel session with 120 minutes lifetime
  - "Ingat Saya" functionality with 5-year remember token
  - Automatic logout on session expiration
- **Security Features**:
  - Rate limiting for login attempts (5 attempts per IP)
  - Account validation (status_aktif check)
  - Password hashing with Laravel's built-in security
  - Default credentials removed from login page

#### Master Data & Wilayah System
- **Hierarchical Structure**: Kelurahan → RW → RT
- **Public API Endpoints**:
  - `/api/keluarga/wilayah` - Cascading dropdown data
  - `/api/keluarga/rt-info` - RT address information
- **Real Sample Data**: 17 wilayah records (Bendul Merisi, Surabaya)
- **Cascading Dropdown**: JavaScript-powered dynamic selection

#### Keluarga Management (KK-First Architecture)
- **Core Architecture**: Warga creation hanya melalui keluarga module
- **Dual Address System**:
  - **Alamat KTP**: Manual input fields (rt_kk, rw_kk, kelurahan_kk, kecamatan_kk, kabupaten_kk, provinsi_kk)
  - **Alamat Domisili**: RT ID selection dengan cascading dropdown (Kelurahan → RW → RT)
- **Foto KK Management**:
  - Upload validation: `mimes:jpeg,jpg,png|max:2048`
  - Storage: `documents/kk/kk_{no_kk}_{timestamp}.{ext}`
  - Display with zoom and download functionality
- **CRUD Operations**: Modal-based AJAX operations
- **Activity Logging**: Complete audit trail untuk semua perubahan

#### Warga Management System
- **Edit-Only Module**: Tidak ada create warga langsung (redirect ke keluarga)
- **Data Structure**: 4 sections dengan focus pada fundamental data
  - Data Pribadi (NIK, nama, tanggal lahir, jenis kelamin, etc.)
  - Data Orang Tua (nama_ayah, nama_ibu)
  - Data Kontak (telepon, email)
  - Data Lainnya (pendidikan, pekerjaan, agama, etc.)
- **Foto KTP System**:
  - Storage: `documents/ktp/ktp_{nik}_{timestamp}.{ext}`
  - Action button with conditional display
  - Modal zoom functionality with download option
- **Enhanced Search**: Auto-trigger (500ms debounce) multi-field search
- **Table Actions**: View, Foto KTP, Edit, Delete dengan proper disabled states

#### Photo Storage & File Management
- **Standardized Structure**:
  ```
  storage/app/public/documents/
  ├── ktp/     - Individual KTP photos
  └── kk/      - Family card photos
  ```
- **File Naming Convention**:
  - KTP: `ktp_{nik}_{timestamp}.{ext}`
  - KK: `kk_{no_kk}_{timestamp}.{ext}`
- **Automatic Cleanup**: Old files removed saat update
- **Consistent Validation**: All photos use same validation rules

#### API & AJAX System
- **Public Routes**: Wilayah data access tanpa authentication
- **Real-time Validation**: JavaScript validation with Laravel backend
- **Modal-based CRUD**: Tidak ada page refresh untuk CRUD operations
- **Error Handling**: Comprehensive error feedback dengan toast notifications
- **Loading States**: Smooth loading animations untuk better UX

### ⚠️ **IN PROGRESS**

#### Iuran Management (Partial Implementation)
- **Basic Models**: Iuran and PembayaranIuran models established
- **Database Conflicts**: Need to resolve warga-iuran relationship issues
- **Architecture Decision**: Should be KK-based, not warga-based

### 🔄 **PLANNED FEATURES**

#### Dashboard & Reporting System
- **Real-time Statistics**: Population demographics, financial summaries
- **Chart Integration**: Chart.js for data visualization
- **Export Functionality**: PDF and Excel export capabilities
- **Filtering**: Date range and wilayah-based filtering

#### Advanced Iuran Management
- **Automatic Billing**: Monthly invoice generation
- **Payment Tracking**: Multiple payment methods integration
- **Tunggakan Management**: Late fee calculation and notifications
- **Financial Reports**: Comprehensive financial reporting

#### Public Portal
- **Data Verification**: Anonymous warga data lookup
- **Iuran Status**: Payment status checking with QR codes
- **Security Measures**: Rate limiting and data filtering
- **Mobile Friendly**: Responsive design for public access

#### System Administration
- **Backup System**: Automated backup and restore
- **Settings Management**: System configuration interface
- **Activity Monitoring**: Comprehensive activity log viewer
- **Performance Metrics**: System performance tracking

---

## 1. Manajemen Pengguna (User Management)

### 1.1 Autentikasi & Autorisasi

**Roles yang tersedia:**
- **Administrator (Admin)**: Kontrol penuh sistem
- **Lurah**: Akses semua data warga, laporan, approval
- **RW**: Akses data warga per RW, laporan RW
- **RT**: Akses data warga per RT, input data

**Permission Matrix:**

| Fitur | Admin | Lurah | RW | RT |
|------|-------|-------|----|----|
| Manajemen User | ✓ | ✗ | ✗ | ✗ |
| Master Data (Iuran/Wilayah) | ✓ | ✗ | ✗ | ✗ |
| Data Warga (Semua RW) | ✓ | ✓ | ✗ | ✗ |
| Data Warga (Per RW) | ✓ | ✓ | ✓ | ✗ |
| Data Warga (Per RT) | ✓ | ✓ | ✓ | ✓ |
| Create/Update Warga | ✓ | ✓ | ✓ | ✓ |
| Delete Warga (Soft) | ✓ | ✓ | ✗ | ✗ |
| Manajemen Iuran | ✓ | ✓ | ✓ | ✓ |
| Laporan (Semua) | ✓ | ✓ | RW saja | RT saja |
| Pengaturan Sistem | ✓ | ✗ | ✗ | ✗ |
| Backup & Restore | ✓ | ✗ | ✗ | ✗ |

### 1.2 User Authentication

**Halaman Login:**
- Input: username, password
- Validation: Laravel Auth + role-based redirect
- Session: Standard Laravel session
- Remember Me: Optional (checkbox)
- Password Reset: Email-based recovery

**Halaman Profile:**
- Edit data diri user (username, email, password)
- Upload foto profile (optional)
- Last login tracking
- Activity log terkait user

### 1.3 User Management (Admin Only)

**CRUD Users:**
- Create: Form input user baru dengan role assignment
- Read: Daftar user dengan filter role dan status
- Update: Edit user data, role, status aktif/non-aktif
- Delete: Soft delete dengan confirmation dialog

**User-Wilayah Assignment:**
- Set wilayah kerja untuk RW/RT
- Multi-select untuk RW (bisa handle beberapa RT)
- Auto-filter untuk hierarki wilayah

---

## 2. Manajemen Data Warga (Citizen Data Management)

### 2.1 Master Data Penduduk

**Data KTP (Data Tetap):**
```
Field            Type        Required    Validation
---------------------------------------------------
nik              VARCHAR(16) ✓           Unique, format 16 digit
nama_lengkap     VARCHAR(100)✓           Alpha + spaces
tempat_lahir     VARCHAR(50) ✓
tanggal_lahir    DATE        ✓           Max today
jenis_kelamin    ENUM(L,P)   ✓
golongan_darah   ENUM(A,B,AB,O) ✗
alamat_ktp       TEXT        ✓
rt_ktp           VARCHAR(3)  ✓           Numeric
rw_ktp           VARCHAR(3)  ✓           Numeric
kelurahan_ktp    VARCHAR(50) ✓
kecamatan_ktp    VARCHAR(50) ✓
kabupaten_ktp    VARCHAR(50) ✓
provinsi_ktp     VARCHAR(50) ✓
agama            ENUM        ✓           Islam,Kristen,Katolik,Hindu,Buddha,Konghucu
status_perkawinan ENUM       ✓           Belum Kawin,Kawin,Cerai Hidup,Cerai Mati
pekerjaan        ENUM        ✓           20+ options
kewarganegaraan  ENUM        ✓           WNI,WNA
pendidikan_terakhir ENUM      ✓           Tidak/Sekolah,SD,SMP,SMA,D1/D2/D3,S1,S2,S3
foto_ktp         FILE        ✗           JPG/PNG max 2MB
```

**Data Kontak Personal:**
```
Field          Type        Required    Validation
---------------------------------------------------
no_telepon     VARCHAR(20) ✗           Phone format
email          VARCHAR(100)✗           Email format
kk_id          BIGINT      ✗           Foreign key ke keluarga
hubungan_keluarga VARCHAR(50)✗         Kepala Keluarga,Istri,Anak,dll
created_by     BIGINT      ✗           Foreign key ke users
updated_by     BIGINT      ✗           Foreign key ke users
```

**Note:** Alamat dan status domisili dipindahkan ke level keluarga

**Note:** Hubungan keluarga dan KK link sudah dihandle di field di atas

### 2.2 CRUD Operations

**Create Warga:**
- Modal form with tabs: Data KTP, Data Kontak, Data Keluarga
- Auto-complete untuk alamat KTP (sesuai wilayah)
- Real-time validation (AJAX)
- NIK validation: check existing NIK
- KK selection: dropdown dari existing KK atau create new KK
- File upload: foto KTP dengan preview

**Read/View Warga:**
- Card view & table view
- Search: NIK, nama, alamat KTP
- Filter: RT/RW (berdasarkan KK), jenis kelamin, agama, pekerjaan, pendidikan
- Sort: nama, tanggal lahir, created_at
- Pagination: 20 data per page
- Export: CSV, PDF (filtered data)
- Display alamat domisili dari data KK

**Update Warga:**
- Modal popup edit form dengan pre-filled data
- History tracking: log perubahan ke aktivitas_log
- Validation untuk NIK unique (kecuali record sendiri)
- KK re-assignment jika berubah KK

**Delete Warga:**
- Soft delete menggunakan deleted_at timestamp
- Confirmation dialog dengan informasi dampak
- Cascade delete untuk data terkait (iuran, dll)
- Recovery option (admin only)

### 2.3 Advanced Features

**Search & Filter:**
- Real-time search dengan debouncing (500ms)
- Multi-criteria filter dengan AND/OR logic
- Saved filters (bookmarking)
- Advanced search dengan custom query builder

**Data Import/Export:**
- Bulk import dari Excel/CSV template
- Validation sebelum import dengan error reporting
- Export dengan custom column selection
- Scheduled export (auto-email)

**Duplicate Detection:**
- Auto-deteksi based on NIK + nama + tanggal lahir
- Merge suggestions untuk data duplikat
- Manual review untuk ambiguous cases

---

## 3. Manajemen Keluarga (Family Management)

### 3.1 Master Data Keluarga

**Data Structure:**
```
# ALAMAT KTP (Input Manual Lengkap)
Field                      Type         Required    Validation
---------------------------------------------------------------
no_kk                      VARCHAR(16)  ✓           Unique, format 16 digit
kepala_keluarga_id         BIGINT       ✗           Foreign key ke warga
alamat_kk                  TEXT         ✓           Alamat lengkap sesuai KK
rt_kk                      VARCHAR(10)  ✓           RT sesuai KK
rw_kk                      VARCHAR(10)  ✓           RW sesuai KK
kelurahan_kk               VARCHAR(100) ✓           Kelurahan sesuai KK
kecamatan_kk               VARCHAR(100) ✓           Kecamatan sesuai KK
kabupaten_kk               VARCHAR(100) ✓           Kabupaten sesuai KK
provinsi_kk                VARCHAR(100) ✓           Provinsi sesuai KK

# ALAMAT DOMISILI (Koneksi Sistem Wilayah)
alamat_domisili            TEXT         ✗           Alamat jalan saja untuk domisili
rt_id                      BIGINT       ✓           Foreign key ke wilayahs.id (tingkat=RT)
rt_domisili                VARCHAR(10)  ✓           Auto-generate dari rt_id
rw_domisili                VARCHAR(10)  ✓           Auto-generate dari rt_id
kelurahan_domisili         VARCHAR(100) ✓           Auto-generate dari rt_id
kecamatan_domisili         VARCHAR(100) ✓           Auto-generate dari rt_id
kabupaten_domisili         VARCHAR(100) ✓           Auto-generate dari rt_id
provinsi_domisili          VARCHAR(100) ✓           Auto-generate dari rt_id

# STATUS & KETERANGAN
status_domisili_keluarga   ENUM         ✓           Tetap,Non Domisili,Luar,Sementara
tanggal_mulai_domisili_keluarga DATE   ✗
keterangan_status          TEXT         ✗
```

### 3.2 Keluarga Form Input System

**Dual Address Input Form Layout:**

**Section 1 - Data KK**
```
Field                  Input Type    Description
--------------------------------------------------
No. KK                 Text 16 digit Format standard KK
```

**Section 2 - Alamat KTP (Input Manual Lengkap)**
```
Field                  Input Type    Required    Description
------------------------------------------------------------------
Alamat Lengkap KK      Textarea      ✓          Alamat sesuai dokumen KK
RT KTP                 Text          ✓          RT sesuai KK (manual)
RW KTP                 Text          ✓          RW sesuai KK (manual)
Kelurahan KTP          Text          ✓          Kelurahan sesuai KK (manual)
Kecamatan KTP          Text          ✓          Kecamatan sesuai KK (manual)
Kabupaten KTP          Text          ✓          Kabupaten sesuai KK (manual)
Provinsi KTP           Text          ✓          Provinsi sesuai KK (manual)
```

**Section 3 - Alamat Domisili (rt_id only - Dynamic Loading)**
```
Field                  Input Type    Required    Description
------------------------------------------------------------------
Alamat Domisili        Text          ✗          Alamat jalan saja
Pilih RT Domisili      Dropdown      ✓          229 RT data dari sistem
↓ Dynamic Loading via rt_id relationship:
RT/RW/Kelurahan/etc    Display-only  ✓          Load otomatis dari wilayah table
Note: Hanya rt_id yang disimpan di database, data lain di-load secara dinamis
```

**Section 4 - Status & Keterangan**
```
Field                  Input Type    Required    Description
------------------------------------------------------------------
Status Domisili        Select        ✓          Tetap/Non Domisili/Luar/Sementara
Tanggal Mulai          Date          ✗          Tanggal mulai domisili
Keterangan             Textarea      ✗          Keterangan tambahan
```

**Section 5 - Multi Input Warga**
```
- Add Warga Button: Tambah form warga dinamis
- Min 1 Kepala Keluarga (required validation)
- Warga Fields: NIK, Nama, Tempat/Tgl Lahir, Jenis Kelamin, Agama,
               Pendidikan, Pekerjaan, Status Perkawinan, No Telepon, Email, Hubungan Keluarga
- Auto-complete untuk existing warga (jika pindah KK)
- Bulk validation untuk NIK duplication
```

**Form Behavior & Validation:**
- **Real-time Validation**: NIK uniqueness, format 16 digit
- **Status Logic**:
  - Jika "Tetap": Alamat KTP = Alamat Domisili (auto-fill suggestion)
  - Jika "Non Domisili": Alamat KTP di sini, domisili di luar
  - Jika "Luar": Alamat KTP di luar, domisili di sini
  - Jika "Sementara": Kontrak/Ngontrak
- **RT Selection**: Dropdown berdasarkan data wilayah yang sudah ada (278 records)
- **Dynamic Loading**: RT selection → load alamat domisili secara real-time via relationship
- **Storage Efficiency**: Hanya rt_id yang disimpan, tidak ada redundant data
- **Real-time Sync**: Perubahan data wilayah langsung refleksi ke semua keluarga
- **Multi-warga**: Dynamic form untuk input beberapa warga sekaligus
- **Kepala Keluarga**: Mandatory validation (minimal 1 Kepala Keluarga)

### 3.3 Family Member Management

**Anggota Keluarga Operations:**
- Add member: search warga existing atau create new
- Remove member: unlink dari KK (delete kk_id di warga)
- Change head of family: update kepala_keluarga_id
- Member relationship management: hubungan_keluarga

**Family Tree View:**
- Hierarchical view dari KK
- Quick navigation antar anggota keluarga
- Export KK data dengan semua anggota

### 3.4 KK Features

**KK Operations:**
- Create new KK dengan automatic head assignment
- KK merge untuk anggota yang pindah KK
- KK split untuk pembentukan KK baru
- KK history tracking perpindahan anggota

**Document Management:**
- Upload scan KK (PDF/JPG)
- Photo gallery untuk dokumen keluarga
- Document expiration tracking

---

## 5. Dashboard & Laporan (Dashboard & Reports)

### 5.1 Dashboard Overview

**Real-time Statistics:**
```
Widget              Data Source            Filter         Refresh Rate
--------------------------------------------------------------------
Total Warga         COUNT(warga)           by RT/RW       Real-time
Warga per RT        GROUP BY rt_domisili   RW filter      Every 5 min
Demografi Age       TIMESTAMPDIFF(YEAR)    All            Every 5 min
Gender Distribution GROUP BY jenis_kelamin All            Every 5 min
Education Stats     GROUP BY pendidikan    All            Every 5 min
Iuran Collection    SUM(pembayaran_iuran)  Monthly        Every hour
Overdue Payments    COUNT(iuran WHERE status='overdue') By RT/RW Every hour
```

**Interactive Charts:**
- Pie chart: Agama, jenis kelamin, pendidikan, pekerjaan
- Bar chart: Warga per RT/RW, iuran collection per bulan
- Line chart: Population growth, iuran trends
- Heat map: Population density per area

**Quick Actions:**
- Add new warga button
- Generate laporan shortcut
- System settings access
- Backup/restore trigger

### 4.2 Reporting System

**Standard Reports:**
1. **Laporan Demografi:**
   - Jumlah penduduk per RT/RW
   - Piramida usia
   - Statistik pendidikan
   - Statistik pekerjaan
   - Agama distribution

2. **Laporan Kependudukan:**
   - Data baru bulanan
   - Pindah datang/keluar
   - Birth/Death records
   - Marriage/Divorce records

3. **Laporan Iuran:**
   - Pembayaran bulanan per RT/RW
   - Outstanding payments
   - Denda collection
   - Payment trends

**Custom Report Builder:**
- Drag-and-drop report designer
- Custom field selection
- Multiple filter criteria
- Chart/graph options
- Scheduled report generation

**Export Options:**
- PDF (landscape/portrait)
- Excel (with formulas)
- CSV (data only)
- Print-friendly format

---

## 5. Manajemen Iuran Warga (Community Fee Management)

### 5.1 Master Data Iuran

**Jenis Iuran Configuration:**
```
Field              Type         Required    Description
-----------------------------------------------------
nama               VARCHAR(50)  ✓           Nama jenis iuran
deskripsi          TEXT         ✗           Detail penjelasan
nominal_default    DECIMAL(10,2)✓           Default amount
periode            ENUM         ✓           Bulanan,Tahunan,Sekali
status_aktif       BOOLEAN      ✓           Active/inactive
```

**Default Iuran Types:**
- Iuran Kebersihan (Bulanan)
- Iuran Keamanan/Satpam (Bulanan)
- Iuran Sosial/Kematian (Bulanan)
- Iuran Infrastruktur (Sekali/Tahunan)

### 5.2 Iuran Billing System

**Automatic Billing:**
- Recurring billing setup (monthly/yearly)
- Prorate calculation untuk resident baru
- Special adjustments (discount/penalty)
- Bulk billing generation per RT/RW

**Billing Features:**
- Generate tagihan bulanan otomatis
- Custom billing per kasus khusus
- Discount management
- Waiver approval system
- Payment due date tracking

**Invoice Management:**
- Digital invoice generation
- WhatsApp/SMS notification
- Email delivery
- Print-ready invoice format
- Batch invoice processing

### 5.3 Payment Processing

**Payment Methods:**
- Cash (tunai) dengan kuitansi
- Bank transfer (tanpa bukti, trust-based)
- Digital payment (QRIS, E-wallet)
- Record audit trail untuk semua pembayaran

**Payment Workflow:**
1. User selects KK/periode/iuran
2. System calculates total (sesuai nominal_custom jika ada)
3. User input payment details
4. Record payment with audit trail
5. Receipt generation
6. Update payment status

**Historical Data Preservation:**
- **Status-Based System**: keluarga.status_keluarga (Aktif/Pindah/Non-Aktif/Dibubarkan)
- **Complete Audit Trail**: Semua iuran dan pembayaran tetap tersimpan
- **Financial Integrity**: Laporan keuangan balance meskipun keluarga berstatus non-aktif
- **Status Management**: Family status changes affect future iuran generation only

**Payment Validation:**
- Amount verification
- Duplicate payment detection
- Payment date validation
- Receipt number generation

### 5.4 Denda & Reminder System

**Denda Keterlambatan:**
- Percentage-based calculation (e.g., 2% per month)
- Maximum denda cap (e.g., max 50% of principal)
- Grace period configuration
- Special case denda waiver

**Automated Reminders:**
- Payment due reminders (7 days, 3 days, 1 day before)
- Overdue notifications (weekly, monthly)
- Multiple channels: WhatsApp, SMS, Email
- Reminder templates customization

**Denda Management:**
- Automatic denda calculation
- Manual denda adjustment
- Denda waiver approval workflow
- Denda reporting per periode

### 5.5 Financial Reporting

**Payment Reports:**
- Daily collection report
- Monthly payment summary
- Year-end financial statement
- Denda collection report

**Analytics:**
- Payment trend analysis
- Default rate per RT/RW
- Revenue forecasting
- Cash flow projections

---

## 6. Pengaturan Sistem (System Settings)

### 6.1 Kelurahan Configuration

**Identitas Kelurahan:**
```
Setting Key          Value Type    Description
------------------------------------------------
kelurahan_nama       VARCHAR(100) Nama Kelurahan
kelurahan_alamat     TEXT         Alamat Kantor Kelurahan
kelurahan_telepon    VARCHAR(15)  Nomor telepon kantor
kelurahan_email      VARCHAR(50)  Email resmi
kepala_kelurahan     VARCHAR(100) Nama Kepala Kelurahan
kepala_nip           VARCHAR(18)  NIP Kepala Kelurahan
sekretaris_kelurahan VARCHAR(100) Nama Sekretaris
sekretaris_nip       VARCHAR(18)  NIP Sekretaris
```

**System Preferences:**
- Default settings untuk aplikasi
- User interface preferences
- Email/SMS templates
- Backup schedule configuration

### 6.2 Master Data Management

**Agama Options:**
- Islam, Kristen, Katolik, Hindu, Buddha, Konghucu
- Custom addition (admin only)

**Pekerjaan Options:**
- Standard BPS categories
- Custom job titles addition
- Employment status tracking

**Pendidikan Options:**
- Standard education levels
- Special education categories
- Institution data tracking

**Status Perkawinan Options:**
- Belum Kawin, Kawin, Cerai Hidup, Cerai Mati
- Legal status verification

### 6.3 System Administration

**Backup & Restore:**
- Automated daily/weekly backup
- Manual backup trigger
- Database restore functionality
- Backup file management

**System Monitoring:**
- User activity logs
- System performance metrics
- Error logging & reporting
- Storage usage monitoring

**Maintenance Mode:**
- Scheduled downtime
- User notification system
- Maintenance logging
- Rollback capabilities

---

## 9. Portal Publik Warga (Public Citizen Portal)

### 9.1 Pencarian Data Warga Publik

**Fitur Pencarian:**
- Search berdasarkan NIK (16 digit) atau nama lengkap
- Captcha verification untuk mencegah bot abuse
- Rate limiting: maksimal 10 pencarian per IP per menit
- Format output yang disensor untuk keamanan data

**Output Data Warga (Disensor):**
```
Field                Display Format      Sensitivity Level
-----------------------------------------------------------
NIK                 316105********1234  Medium (sensor 10 digit)
Nama Lengkap        John Doe           Low
Tempat Lahir        J***karta          High (sensor >3 char)
Tanggal Lahir       ***-**-****        High
Jenis Kelamin       L/P               Low
Alamat KTP          Jl. *** No. **   Medium (sensor detail)
Alamat Domisili     Jl. *** RT 001    Medium (dari data KK)
RT/RW Domisili      001/002           Low (dari data KK)
Kelurahan Domisili  Kelurahan X       Low (dari data KK)
Status Perkawinan   Kawin             Low
Hubungan Keluarga   Kepala Keluarga   Low (dari data KK)
```

### 9.2 Cek Status Keluarga

**Input:** Nomor KK (16 digit)
**Verification:** Captcha + rate limiting

**Output Keluarga (Disensor):**
```
Field                        Display Format      Sensitivity Level
-----------------------------------------------------------------
No KK                       316105********1234  Medium (sensor 10 digit)
Nama Kepala Keluarga         Budi S****o       Medium (sensor 5 char)
Jumlah Anggota              4 orang           Low
Alamat KK                   Jl. *** RT 001    Medium (sensor detail)
RT/RW KK                    001/002           Low
Kelurahan KK                Kelurahan X       Low
Status Domisili Keluarga    Tetap            Low
Daftar Anggota              [Daftar anak 18+]  Medium
Tanggal Mulai Domisili      ***-**-****      High
```

### 9.3 Monitoring Iuran Publik

**Input:** NIK atau Nomor KK
**Output Status Iuran:**
```
Field                Display Format      Notes
-----------------------------------------------------------
Bulan Bayar         Jan 2024, Feb 2024  Maximum 12 bulan terakhir
Tunggakan           Rp 150.000        Total tunggakan
Status              Lunas/Belum     Status pembayaran terkini
Nominal Iuran       Rp 25.000/bulan  Tanpa detail sensitif
```

### 9.4 Keamanan Public Portal

**Security Measures:**
- **Rate Limiting:** IP-based (10 requests/minute)
- **Captcha:** Google reCAPTCHA v2 untuk verification
- **IP Tracking:** Log semua aktivitas dengan IP address
- **Request Logging:** Semua search dicatat untuk audit
- **Data Sanitization:** Otomatis sensor data sensitif
- **Session Timeout:** 5 menit untuk session publik

**Data Protection:**
- **NIK/No KK:** Sensor 10 digit tengah
- **Nama Lengkap:** Sensor 5+ karakter jika di atas 5
- **Alamat:** Sensor nomor rumah dan detail jalan
- **Telepon:** Tidak ditampilkan sama sekali
- **Email:** Tidak ditampilkan sama sekali
- **Tanggal Lahir:** Format ***-**-**** untuk privacy

### 9.5 Informasi Umum Publik

**Halaman Statis:**
- Alur dan prosedur pembayaran iuran
- Jadwal pembayaran bulanan per RT/RW
- Kontak petugas RT/RW (nama saja, tanpa nomor telepon)
- Pengumuman kelurahan terbaru
- FAQ (Frequently Asked Questions)

**API Endpoint Public:**
- `GET /public/cek-warga` - Pencarian data warga
- `GET /public/cek-keluarga` - Cek status keluarga
- `GET /public/cek-iuran` - Monitoring pembayaran iuran
- `GET /public/info-umum` - Informasi kelurahan

### 9.6 Keamanan Tambahan

**Monitoring & Logging:**
- Log semua request dengan timestamp dan IP
- Alert untuk suspicious activity pattern
- Automatic IP blocking untuk abuse detection
- Daily report untuk administrator

**Rate Limiting Rules:**
- **Search Warga:** 5 requests/minute per IP
- **Cek Iuran:** 10 requests/minute per IP
- **Cek Keluarga:** 3 requests/minute per IP
- **Global:** 20 requests/minute per IP

---

## 10. Technical Specifications

### 10.1 Frontend Requirements

**UI Framework:**
- SB Admin 2 template
- Bootstrap 4.6+ for responsive design
- jQuery 3.7+ for DOM manipulation
- Font Awesome 6.0+ for icons

**Interactive Components:**
- DataTables for dynamic tables
- Chart.js for data visualization
- Select2 for advanced dropdowns
- Date picker (flatpickr)
- File upload with preview
- Modal dialogs for confirmations

**User Experience:**
- Full AJAX-based operations (NO PAGE REFRESH untuk semua operasi)
- Popup-based CRUD operations (modal dialogs)
- Loading indicators & spinners
- Toast notifications untuk feedback
- Form validation dengan real-time feedback
- Keyboard shortcuts untuk power users
- Mobile responsive design

**Performance Requirements:**
- Page load time < 3 seconds
- AJAX response < 1 second
- Image optimization & lazy loading
- Caching strategy for static assets
- Progressive web app features

### 10.2 Backend Requirements

**Laravel Configuration:**
- Laravel 12 with PHP 8.3+
- Laravel Breeze for authentication
- Eloquent ORM with soft deletes
- Resource controllers with API support
- Form request validation
- Queue system for background jobs

**Security Measures:**
- CSRF protection
- XSS prevention
- SQL injection prevention
- Input sanitization & validation
- Rate limiting for API endpoints
- Password hashing (bcrypt)
- Role-based access control

**API Specifications:**
- RESTful API design
- API resource formatting
- Consistent response structure
- Error handling with proper status codes
- API versioning support
- API documentation (Swagger/OpenAPI)

### 10.3 Database Optimization

**Indexing Strategy:**
- Primary keys on all tables
- Foreign key indexes
- Search indexes (nik, nama, alamat)
- Composite indexes for common queries
- Full-text search for address fields

**Query Optimization:**
- Eager loading for relationships
- Query result caching
- Database read/write separation
- Connection pooling
- Slow query logging

**Data Integrity:**
- Foreign key constraints
- Unique constraints (NIK, KK)
- Check constraints for enums
- Trigger for audit logging
- Soft delete implementation

---

## 11. Development Guidelines

### 11.1 Code Standards

**PHP Standards:**
- PSR-12 coding standards
- Laravel best practices
- Clean code principles
- Meaningful variable names
- Proper documentation (PHPDoc)

**JavaScript Standards:**
- ES6+ features
- Modular code organization
- Proper error handling
- Code minification for production
- Cross-browser compatibility

**CSS Standards:**
- SCSS for preprocessing
- BEM methodology for class naming
- Mobile-first responsive design
- CSS optimization & minification
- Browser compatibility testing

### 11.2 Testing Requirements

**Unit Testing:**
- Model testing with factories
- Controller testing with mock data
- Service layer testing
- Utility function testing
- Minimum 80% code coverage

**Integration Testing:**
- API endpoint testing
- Database transaction testing
- File upload testing
- Email notification testing
- Third-party service testing

**Browser Testing:**
- Selenium for end-to-end testing
- Cross-browser compatibility
- Mobile device testing
- Accessibility testing
- Performance testing

### 11.3 Deployment Strategy

**Environment Setup:**
- Development environment (Docker)
- Staging environment (production mirror)
- Production environment with load balancing
- CI/CD pipeline implementation
- Automated testing on deployment

**Monitoring & Logging:**
- Application performance monitoring
- Error tracking & alerting
- User behavior analytics
- System resource monitoring
- Security audit logging

---

## 12. Project Timeline

### 12.1 Development Phases

**Phase 1: Foundation (Week 1-2)**
- Laravel project setup
- Database design implementation
- Authentication system
- Basic UI framework integration

**Phase 2: Core Features (Week 3-4)**
- User management system
- Warga CRUD operations
- KK management system
- Basic dashboard

**Phase 3: Advanced Features (Week 5-6)**
- Iuran management system
- Reporting system
- Advanced search & filtering
- File upload & document management

**Phase 4: Integration & Testing (Week 7-8)**
- System integration
- Performance optimization
- Security testing
- User acceptance testing

**Phase 5: Deployment (Week 9-10)**
- Production deployment
- Data migration
- User training
- Go-live support

**Phase 6: Portal Publik (Week 11-12)**
- Public portal development
- Data sanitization implementation
- Security measures (captcha, rate limiting)
- Public API development

### 12.2 Deliverables

**Weekly Deliverables:**
- Source code with Git commits
- Progress reports
- Demo sessions
- Technical documentation

**Final Deliverables:**
- Complete source code
- Database schema
- API documentation
- User manual
- Technical documentation
- Deployment guide

---

## 13. Success Criteria

### 13.1 Functional Requirements
- ✅ All features from brief implemented
- ✅ 4-level role management working
- ✅ Complete citizen data management
- ✅ Financial management system operational
- ✅ Reporting system functional
- ✅ Data security & integrity maintained
- ✅ Public portal with data sanitization
- ✅ Public security measures implemented
- ✅ **UPDATED**: Alamat & status domisili moved to keluarga level
- ✅ **UPDATED**: Real Bendul Merisi data structure implemented
- ✅ **UPDATED**: Database migration & seeding system complete

### 13.2 Non-Functional Requirements
- ✅ System response time < 3 seconds
- ✅ 99% uptime availability
- ✅ Mobile responsive design
- ✅ Cross-browser compatibility
- ✅ Security audit passed
- ✅ User training completed

### 13.3 Acceptance Criteria
- Successful user acceptance testing
- Performance benchmarks met
- Security requirements satisfied
- Documentation complete
- Training program delivered
- Go-live approval obtained

---

*Dokumen ini akan menjadi panduan utama untuk tim development dalam implementasi Sistem Informasi Warga (SIWA) Kelurahan.*