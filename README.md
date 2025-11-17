# SIWA - Sistem Informasi Warga Kelurahan

A comprehensive Laravel-based management system for residential community data, citizen information, and community fee tracking. Built with modern web technologies and featuring role-based access control.

## 🏠 Tentang SIWA

**SIWA (Sistem Informasi Warga)** adalah sistem manajemen data kependudukan dan administrasi keuangan tingkat RT/RW yang dikembangkan dengan Laravel 12. Sistem ini dirancang untuk memudahkan pengelolaan data warga, kartu keluarga, pembayaran iuran, dan laporan kependudukan secara digital.

### ✨ Fitur Utama

#### 🔐 **Sistem Keamanan & Akses Multi-Level**
- **4 Level Role**: Admin, Lurah, RW, RT
- **Autentikasi Laravel Breeze** dengan session management
- **Proteksi Middleware** untuk setiap route
- **Activity Logging** untuk audit trail
- **Password Reset** dengan auto-generation

#### 👥 **Manajemen Data Warga**
- **Data Kependudukan Lengkap**: NIk, nama, tempat/tanggal lahir, agama, pendidikan, pekerjaan
- **Foto Profile Warga** dengan upload dan optimasi
- **Hubungan Keluarga**: Kepala keluarga, istri, anak, dll
- **Status Kependudukan**: Tetap, Pendatang, Pindah, Meninggal
- **Soft Deletes** untuk data protection

#### 🏡 **Manajemen Keluarga & Kartu Keluarga**
- **No. KK** dan data lengkap keluarga
- **Multi-step Form** untuk input data keluarga
- **Anggota Keluarga Management** dengan popup
- **Status Keluarga**: Miskin, Tidak Mampu, Mampu

#### 💰 **Sistem Iuran & Pembayaran**
- **Multiple Jenis Iuran**: Iuran kebersihan, keamanan, sosial, dll
- **Flexible Billing**: Bulanan, tahunan, tidak tetap
- **Pembayaran Tracking** dengan status lunas/belum
- **Bukti Pembayaran** digital dengan upload
- **Tunggakan Management** dan reminder system

#### 📊 **Dashboard & Analytics**
- **Role-based Dashboard**: Admin, Lurah, RW, RT
- **Real-time Statistics**: Total warga, keluarga, pembayaran
- **Chart.js Integration** untuk visualisasi data
- **Recent Activities** tracking

#### 🔧 **Admin Panel & Settings**
- **User Management** dengan role assignment
- **Wilayah Management**: Kelurahan, RW, RT
- **Master Data Management**
- **Backup & Restore System**
- **System Settings & Configuration**

#### 🌐 **Portal Publik**
- **Public Access** untuk cek data warga
- **Data Sanitization** (sensor nomor & informasi sensitif)
- **Iuran Status Check** untuk warga
- **CAPTCHA Protection** untuk security
- **Rate Limiting** untuk prevent abuse

#### 📱 **Modern UI/UX**
- **SB Admin 2 Template** dengan Bootstrap 5.3+
- **Modal Popup System** (TANPA PAGE REFRESH)
- **Responsive Design** untuk mobile & desktop
- **Loading Indicators** dan toast notifications
- **White Navigation Theme** untuk better readability

## 🛠️ Teknologi

### Backend
- **Laravel 12** dengan PHP 8.3+
- **MySQL Database** dengan soft deletes
- **Eloquent ORM** dengan relationships
- **Laravel Breeze** untuk authentikasi
- **File Upload** dengan image optimization

### Frontend
- **Bootstrap 5.3+** dengan SB Admin 2 theme
- **jQuery 3.7+** untuk AJAX operations
- **Chart.js** untuk data visualization
- **Font Awesome** untuk icons
- **Google Fonts** (Nunito)

### Security Features
- **CSRF Protection**
- **Input Validation** & sanitization
- **SQL Injection Protection**
- **Rate Limiting** untuk public API
- **Role-based Access Control**

## 📋 Prerequisites

```bash
# PHP Requirements
PHP >= 8.3
composer
npm (optional)

# Database
MySQL 8.0+ atau MariaDB 10.3+

# Web Server
Apache dengan mod_rewrite atau Nginx
```

## 🚀 Installation

### 1. Clone Repository
```bash
git clone https://github.com/syanamprox/siwa-bm.git
cd siwa-bm
```

### 2. Install Dependencies
```bash
composer install
npm install
npm run build
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siwa
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Database Migration
```bash
php artisan migrate
php artisan db:seed
```

### 6. File Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 7. Link Storage
```bash
php artisan storage:link
```

## 🔑 Default Users

Setelah instalasi, login dengan default users:

### **Super Admin**
- **Username**: `admin`
- **Password**: `password`
- **Akses**: Semua fitur sistem

### **Lurah**
- **Username**: `lurah`
- **Password**: `password`
- **Akses**: Dashboard, Laporan, User Management

### **RW**
- **Username**: `rw`
- **Password**: `password`
- **Akses**: Data Warga, Keluarga, Iuran

### **RT**
- **Username**: `rt`
- **Password**: `password`
- **Akses**: Data Warga, Iuran level RT

## 🌐 Access URLs

- **Main Dashboard**: `http://localhost:8000/dashboard`
- **Login**: `http://localhost:8000/login`
- **Admin Users**: `http://localhost:8000/admin/users`
- **Portal Publik**: `http://localhost:8000/portal`

## 📁 Project Structure

```
siwa/
├── app/
│   ├── Http/Controllers/          # Controllers
│   ├── Models/                    # Eloquent Models
│   ├── Middleware/                # Custom Middleware
│   └── Providers/                 # Service Providers
├── database/
│   ├── migrations/                # Database Migrations
│   └── seeders/                   # Database Seeders
├── resources/
│   ├── views/                     # Blade Templates
│   │   ├── layouts/              # Main Layouts
│   │   ├── dashboard/            # Dashboard Views
│   │   ├── admin/                # Admin Panel Views
│   │   └── portal/               # Public Portal Views
│   └── js/                       # JavaScript Files
├── routes/                       # Route Definitions
├── storage/                      # File Storage
├── public/                       # Public Assets
└── documentation/                # Documentation Files
```

## 💾 Database Schema

### **Core Tables**
- `users` - Data pengguna sistem
- `wilayah` - Master data wilayah (Kelurahan, RW, RT)
- `warga` - Data kependudukan warga
- `keluarga` - Data kartu keluarga
- `jenis_iuran` - Master data jenis iuran
- `iuran` - Data iuran per warga
- `pembayaran_iuran` - Data pembayaran
- `aktivitas_log` - Audit trail system

## 🔧 Development

### Run Development Server
```bash
php artisan serve
```

### Generate Application Key
```bash
php artisan key:generate
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Create New Migration
```bash
php artisan make:migration create_table_name
```

### Create New Controller
```bash
php artisan make:controller ControllerName
```

## 📝 API Documentation

### **Public Portal API**
- `GET /portal` - Portal homepage
- `POST /portal/cek-warga` - Cek data warga
- `POST /portal/cek-keluarga` - Cek data keluarga
- `POST /portal/cek-iuran` - Cek status iuran

### **Authenticated API**
- `GET /api/users` - List users (Admin only)
- `POST /api/users` - Create user (Admin only)
- `PUT /api/users/{id}` - Update user (Admin only)
- `DELETE /api/users/{id}` - Delete user (Admin only)

## 🔒 Security Features

### **Authentication & Authorization**
- Laravel Breeze authentication
- Role-based middleware
- Session management
- Password hashing

### **Input Validation & Sanitization**
- Server-side validation rules
- XSS protection
- SQL injection prevention
- File upload validation

### **Rate Limiting & Protection**
- API rate limiting
- CAPTCHA protection
- CSRF tokens
- IP blocking attempts

## 📊 Monitoring & Logging

### **Activity Logging**
Semua aktivitas penting di-log untuk audit:
- User creation, update, delete
- Data warga changes
- Payment transactions
- System settings changes

### **Error Handling**
- Custom error pages
- Exception handling
- Validation error messages
- Logging system errors

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and questions:
- **Email**: admin@siwa.local
- **GitHub Issues**: [Issues Page](https://github.com/syanamprox/siwa-bm/issues)
- **Documentation**: [Wiki](https://github.com/syanamprox/siwa-bm/wiki)

## 🔄 Version History

### **v1.0.0 (Latest)**
- ✅ Complete Laravel 12 setup
- ✅ 4-level role system (Admin, Lurah, RW, RT)
- ✅ SB Admin 2 template integration
- ✅ Modal popup CRUD (no page refresh)
- ✅ User Management system
- ✅ Dashboard with statistics
- ✅ Public portal with data sanitization
- ✅ Activity logging system
- ✅ Responsive design

### **Upcoming Features**
- 📧 Email notifications
- 📱 Mobile app integration
- 📊 Advanced reporting
- 🗺️ Geographic mapping
- 🔔 Push notifications

---

**SIWA** - *Sistem Informasi Warga untuk Kelurahan Modern*

🏠 **Membangun Kelurahan Digital dengan Teknologi Terkini**