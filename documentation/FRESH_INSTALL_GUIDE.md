# 📋 SIWA - Fresh Install Guide

## 🔄 Complete Rollback & Fresh Installation Guide

### ⚠️ **WARNING**
This guide will completely reset your SIWA database and all data will be lost!
Make sure you have backup before proceeding.

---

## 🗂️ **Step 1: Complete Database Reset**

### 1.1 Rollback All Migrations
```bash
php artisan migrate:reset
```
**Expected Output:**
```
INFO Rolling back migrations.
2025_11_17_081659_add_status_aktif_to_users_table ............ 254.72ms DONE
2025_11_17_080613_create_user_wilayahs_table .................. 64.67ms DONE
...
(all migrations rolled back)
```

### 1.2 Verify Database is Empty
```bash
php artisan tinker --execute="echo 'Tables: ' . count(DB::select('SHOW TABLES'));"
```
Should show minimal Laravel tables only.

---

## 🚀 **Step 2: Fresh Migration**

### 2.1 Run All Migrations
```bash
php artisan migrate
```
**Expected Output:**
```
INFO Running migrations.
2025_11_17_073721_create_users_table .......................... 403.75ms DONE
2025_11_17_073730_create_wilayahs_table ...................... 567.78ms DONE
2025_11_17_073739_create_wargas_table .............................. 1s DONE
2025_11_17_073740_create_keluargas_table ........................... 1s DONE
...
(16 migrations completed)
```

### 2.2 Verify Database Structure
```bash
php artisan tinker --execute="
echo 'Tables created:' . PHP_EOL;
\$tables = DB::select('SHOW TABLES');
foreach(\$tables as \$table) {
    foreach(\$table as \$key => \$value) {
        echo '- ' . \$value . PHP_EOL;
    }
}
```

---

## 🌱 **Step 3: Seed Initial Data**

### 3.1 Run Database Seeder
```bash
php artisan db:seed
```
**Expected Output:**
```
INFO Seeding database.
Database\Seeders\UserSeeder ........................................ RUNNING
Database\Seeders\UserSeeder .................................. 2,110 ms DONE

Database\Seeders\WilayahSeeder ..................................... RUNNING
✅ Wilayah data seeded successfully!
📍 Kelurahan: Bendul Merisi
🏘️ RW: 1-12
🏠 RT: RW 03 memiliki 4 RT
📊 Total: 1 Kelurahan + 12 RW + 4 RT = 17 wilayah
Database\Seeders\WilayahSeeder .................................. 52 ms DONE

Database\Seeders\KeluargaSeeder .................................... RUNNING
✅ Keluarga data seeded successfully!
👨‍👩‍👧‍👦 Total: 5 keluarga
Database\Seeders\KeluargaSeeder ................................. 47 ms DONE

Database\Seeders\WargaSeeder ....................................... RUNNING
✅ Warga data seeded successfully!
👥 Total: 5 warga
Database\Seeders\WargaSeeder .................................... 99 ms DONE

Database\Seeders\PengaturanSistemSeeder ............................ RUNNING
✅ Pengaturan Sistem data seeded successfully!
⚙️ Total: 12 pengaturan sistem
Database\Seeders\PengaturanSistemSeeder ......................... 11 ms DONE

Database\Seeders\JenisIuranSeeder .................................. RUNNING
✅ Jenis Iuran data seeded successfully!
💰 Total: 6 jenis iuran
Database\Seeders\JenisIuranSeeder ............................... 12 ms DONE
```

---

## ✅ **Step 4: Verify Installation**

### 4.1 Check Data Counts
```bash
php artisan tinker --execute="
echo '🔍 Database Verification:' . PHP_EOL;
echo '👥 Users: ' . App\Models\User::count() . PHP_EOL;
echo '📍 Wilayah: ' . App\Models\Wilayah::count() . PHP_EOL;
echo '👨‍👩‍👧‍👦 Keluarga: ' . App\Models\Keluarga::count() . PHP_EOL;
echo '👤 Warga: ' . App\Models\Warga::count() . PHP_EOL;
echo '⚙️ Pengaturan: ' . App\Models\PengaturanSistem::count() . PHP_EOL;
echo '💰 Jenis Iuran: ' . App\Models\JenisIuran::count() . PHP_EOL;
"
```

**Expected Results:**
```
🔍 Database Verification:
👥 Users: 7
📍 Wilayah: 129
👨‍👩‍👧‍👦 Keluarga: 5
👤 Warga: 5
⚙️ Pengaturan: 12
💰 Jenis Iuran: 6
```

### 4.2 Verify User Accounts
```bash
php artisan tinker --execute="
echo '👑 User Accounts:' . PHP_EOL;
\$users = App\Models\User::all(['name', 'email', 'role', 'status_aktif']);
foreach(\$users as \$user) {
    \$status = \$user->status_aktif ? '✅ AKTIF' : '❌ NONAKTIF';
    echo \$user->role . ': ' . \$user->name . ' (' . \$user->email . ') - ' . \$status . PHP_EOL;
}
"
```

**Expected Users:**
- **admin**: Administrator (admin@siwa.test) - ✅ AKTIF
- **lurah**: RULLY PRASETYA NEGARA (lurah@siwa.test) - ✅ AKTIF
- **rw03**: BAMBANG SETYAWAN (rw03@siwa.test) - ✅ AKTIF
- **rt01**: TRI BAGUS WAHYUDI (rt01@siwa.test) - ✅ AKTIF
- **rt02**: AKHMAD SURYADI (rt02@siwa.test) - ✅ AKTIF
- **rt03**: M. YASIN (rt03@siwa.test) - ✅ AKTIF
- **rt04**: SULICHAH (rt04@siwa.test) - ✅ AKTIF

### 4.3 Verify Wilayah Hierarchy
```bash
php artisan tinker --execute="
echo '📍 Wilayah Hierarchy:' . PHP_EOL;
\$kelurahan = App\Models\Wilayah::where('tingkat', 'Kelurahan')->first();
echo \$kelurahan->nama . PHP_EOL;

\$rws = App\Models\Wilayah::where('tingkat', 'RW')->get();
foreach(\$rws as \$rw) {
    echo '  ↳ ' . \$rw->nama . PHP_EOL;
    \$rts = App\Models\Wilayah::where('parent_id', \$rw->id)->get();
    foreach(\$rts as \$rt) {
        echo '    ↳ ' . \$rt->nama . PHP_EOL;
    }
}
"
```

---

## 🔧 **Step 5: Optional Configuration**

### 5.1 Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 5.2 Optimize Production (Optional)
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5.3 Storage Links
```bash
php artisan storage:link
```

---

## 🧪 **Step 6: Final Testing**

### 6.1 Test Login
```bash
# Test admin login
curl -X POST http://127.0.0.1:8000/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "admin@siwa.test",
    "password": "admin123"
  }'
```

### 6.2 Test Key Modules
- **User Management**: http://127.0.0.1:8000/admin/users
- **Wilayah Management**: http://127.0.0.1:8000/admin/wilayah
- **Dashboard**: http://127.0.0.1:8000/admin/dashboard

---

## ⚡ **Quick One-Command Reset (Advanced)**

For experienced users, you can combine steps:

```bash
# Complete reset and fresh install in one command
php artisan migrate:reset && php artisan migrate && php artisan db:seed
```

---

## 🚨 **Troubleshooting**

### Common Issues & Solutions:

#### Issue 1: Migration Error
**Problem:** `SQLSTATE[HY000]: General error: 1005 Can't create table`
**Solution:**
```bash
php artisan migrate:reset
php artisan migrate
```

#### Issue 2: Seeder Error
**Problem:** `Table doesn't exist`
**Solution:** Ensure migrations completed successfully before seeding

#### Issue 3: Permission Error
**Problem:** `Permission denied`
**Solution:**
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

#### Issue 4: Foreign Key Constraint
**Problem:** Integrity constraint violation
**Solution:** Complete reset with `migrate:reset` then `migrate`

---

## 📊 **Expected Final State**

After successful fresh installation:

### 📋 **Data Summary:**
- **7 Users** (All active)
- **129 Wilayah** (8 Kelurahan + 47 RW + 74 RT)
  - **Bendul Merisi**: 1 Kelurahan + 12 RW + 4 RT
  - **Wonocolo Area**: 7 Kelurahan + 35 RW + 70 RT
    - Darmo: 1 Kelurahan + 5 RW + 10 RT
    - Jagir: 1 Kelurahan + 4 RW + 8 RT
    - Ngagel: 1 Kelurahan + 6 RW + 12 RT
    - Wonokusumo: 1 Kelurahan + 4 RW + 8 RT
    - Wonocolo: 1 Kelurahan + 6 RW + 12 RT
    - Sawahan: 1 Kelurahan + 5 RW + 10 RT
    - Ketintang: 1 Kelurahan + 5 RW + 10 RT
- **5 Keluarga** (Sample families)
- **5 Warga** (Sample residents)
- **12 Pengaturan Sistem** (System settings)
- **6 Jenis Iuran** (Fee types)

### 🏗️ **System Features Ready:**
- ✅ **Authentication**: Login with email/name + password
- ✅ **User Management**: CRUD with soft delete
- ✅ **Wilayah Management**: Hierarchical structure
- ✅ **Activity Logging**: Complete audit trail
- ✅ **Toast Notifications**: User feedback system
- ✅ **Role-based Access**: 4-level hierarchy

### 🔐 **Default Login Credentials:**
- **Admin**: admin@siwa.test / admin123
- **Lurah**: lurah@siwa.test / lurah123
- **RW**: rw03@siwa.test / rw123
- **RT**: rt01@siwa.test / rt123

---

## 🎉 **Success!**

Your SIWA (Sistem Informasi Warga) installation is now complete and ready for use!

**Next Steps:**
1. Login as admin
2. Explore all modules
3. Add your real data
4. Configure system settings
5. Train users

For support, check the [Documentation](./README.md) or contact development team.