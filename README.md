# **🚗 Rental Mobil - Sukses Lancar Rejeki**

## **📋 DAFTAR ISI**
- [Prasyarat Sistem](#-prasyarat-sistem)
- [Struktur Folder](#-struktur-folder)
- [Instalasi & Setup](#-instalasi--setup)
- [Database](#-database)
- [Login Default](#-login-default)
- [Penjelasan File & Fitur](#-penjelasan-file--fitur)
- [Troubleshooting](#-troubleshooting)

---

## **🖥️ PRASYARAT SISTEM**

| Komponen | Versi Minimal | Keterangan |
|----------|--------------|------------|
| PHP | 7.4+ | Support password_hash() dan session |
| MySQL | 5.7+ | Atau MariaDB 10.4+ |
| Web Server | Apache/Nginx | Bisa pakai built-in PHP server |
| Extensions | mysqli, fileinfo, GD | Untuk koneksi DB dan upload gambar |
| Browser | Chrome/Firefox/Edge | Versi terbaru |

---

## **📁 STRUKTUR FOLDER**

```
rental_mobil/
├── public/                    # File akses publik
│   ├── admin/                # Dashboard admin
│   │   ├── dashboard.php     # Dashboard utama admin
│   │   ├── bookings.php      # Kelola booking
│   │   └── login.php         # Login admin
│   ├── assets/               # CSS, JS, gambar
│   │   ├── css/
│   │   │   └── style.css     # Stylesheet utama
│   │   └── img/
│   ├── uploads/              # Folder upload gambar
│   │   ├── cars/             # Gambar mobil
│   │   └── proofs/           # Bukti pembayaran
│   ├── index.php             # Halaman utama
│   ├── cars.php              # Daftar mobil
│   ├── car_detail.php        # Detail mobil
│   ├── booking.php           # Form booking
│   ├── booking_history.php   # Riwayat booking user
│   ├── login.php             # Login user
│   ├── register.php          # Registrasi user
│   └── navbar.php            # Navigation bar
├── app/
│   ├── config/
│   │   └── db.php            # Konfigurasi database
│   └── controllers/          # Controller aplikasi
│       ├── AuthController.php
│       ├── BookingController.php
│       ├── AdminController.php
│       └── AdminBookingController.php
├── create_admin.php          # Script buat admin
├── tes_booking.php           # Debug booking
└── rental_mobil.sql          # Database structure
```

---

## **⚙️ INSTALASI & SETUP**

### **1. Clone/Download Project**
```bash
# Letakkan di folder web server (htdocs/www)
cp -r rental_mobil /var/www/html/
# atau
mv rental_mobil C:/xampp/htdocs/
```

### **2. Setup Database**
```sql
-- Cara 1: Via phpMyAdmin
1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Buat database baru: rental_mobil
3. Import file: rental_mobil.sql

-- Cara 2: Via Command Line
mysql -u root -p
CREATE DATABASE rental_mobil;
USE rental_mobil;
SOURCE rental_mobil.sql;
```

### **3. Konfigurasi Database**
Edit file: `app/config/db.php`
```php
$db_host = "localhost";      // Sesuaikan host
$db_user = "root";           // Username MySQL
$db_pass = "";               // Password MySQL
$db_name = "rental_mobil";   // Nama database
```

### **4. Setup Folder Uploads**
```bash
# Berikan permission write
chmod 755 public/uploads/
chmod 755 public/uploads/cars/
chmod 755 public/uploads/proofs/

# Atau di Windows, pastikan folder bisa diwrite
```

### **5. Buat Admin User**
```bash
# Akses di browser:
http://localhost/rental_mobil/create_admin.php

# HAPUS file setelah admin dibuat:
rm create_admin.php
```

---

## **🗃️ DATABASE**

### **Struktur Tabel**

| Tabel | Deskripsi | Primary Key | Foreign Keys |
|-------|-----------|-------------|--------------|
| **users** | Data pengguna dan admin | user_id | - |
| **cars** | Data kendaraan | car_id | - |
| **car_images** | Gambar mobil | image_id | car_id → cars.car_id |
| **bookings** | Data booking | booking_id | user_id → users.user_id<br>car_id → cars.car_id |
| **payment_proof** | Bukti pembayaran | proof_id | booking_id → bookings.booking_id |

### **Relasi Database**
```
users (1) ────── (many) bookings (1) ────── (1) cars
    │                                           │
    │                                           │
    └─────── (1) payment_proof                  └─────── (many) car_images
```

---

## **🔐 LOGIN DEFAULT**

| Role | Email | Password | URL Login |
|------|-------|----------|-----------|
| **Admin** | admin@gmail.com | admin123 | http://localhost/rental_mobil/public/admin/login.php |
| **User** | Register manual | - | http://localhost/rental_mobil/public/register.php |

---

## **📄 PENJELASAN FILE & FITUR**

### **1. 🔐 AUTENTIKASI: Login/Logout dengan Session & Password Hash**

| File | Lokasi | Fitur Autentikasi | Keterangan |
|------|--------|-------------------|------------|
| **AuthController.php** | `app/controllers/` | ✅ Login/Logout<br>✅ Session management<br>✅ Password hashing | Controller utama auth |
| **login.php** | `public/` | ✅ Form login user<br>✅ Session check<br>✅ Flash messages | Frontend user login |
| **admin/login.php** | `public/admin/` | ✅ Form login admin<br>✅ Role validation | Admin-specific login |
| **register.php** | `public/` | ✅ Form registrasi<br>✅ Password validation | User registration |
| **navbar.php** | `public/` | ✅ Dynamic menu by session<br>✅ User info display | Navigation dengan session |
| **booking.php** | `public/` | ✅ Access control by session | Cek login sebelum booking |
| **create_admin.php** | Root | ✅ Create admin with hashed password | Script one-time use |

**Implementasi Password Hash:**
```php
// Register: Hash password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Login: Verify password
if (!password_verify($password, $user['password'])) {
    throw new Exception("Email atau password salah");
}
```

---

### **2. 📝 CRUD LENGKAP: Create, Read, Update, Delete**

#### **CRUD untuk Mobil (Cars)**
| File | Create | Read | Update | Delete | Deskripsi |
|------|--------|------|--------|--------|-----------|
| **cars.php** | - | ✅ List all<br>✅ Search/Filter | - | - | Daftar mobil |
| **car_detail.php** | - | ✅ Single detail | - | - | Detail mobil |
| **admin/dashboard.php** | ✅ Add modal | ✅ Table view | ✅ Status update | ✅ Delete car | Admin dashboard |
| **AdminController.php** | ✅ addCar() | - | - | - | Controller add |

#### **CRUD untuk Booking**
| File | Create | Read | Update | Delete | Deskripsi |
|------|--------|------|--------|--------|-----------|
| **booking.php** | ✅ Form | - | - | - | Form booking |
| **booking_history.php** | - | ✅ User's history | - | - | Riwayat user |
| **admin/bookings.php** | - | ✅ All bookings | ✅ Status update | ✅ Cancel | Admin manage |
| **BookingController.php** | ✅ store() | - | - | - | Process booking |
| **AdminBookingController.php** | - | - | ✅ confirm/finish | ✅ cancel | Admin actions |

#### **CRUD untuk Users**
| File | Create | Read | Update | Delete | Deskripsi |
|------|--------|------|--------|--------|-----------|
| **register.php** | ✅ Form | - | - | - | Form register |
| **AuthController.php** | ✅ register() | - | - | - | Save to DB |

**Contoh Query CRUD:**
```sql
-- CREATE
INSERT INTO cars (car_name, brand, price_per_day) VALUES (...);

-- READ
SELECT * FROM cars WHERE status = 'available';

-- UPDATE
UPDATE bookings SET status = 'confirmed' WHERE booking_id = 1;

-- DELETE (soft delete via status)
UPDATE bookings SET status = 'cancelled' WHERE booking_id = 1;
```

---

### **3. 📁 UPLOAD FILE: Upload Gambar/Dokumen**

| File | Tipe File | Validasi | Lokasi Simpan | Deskripsi |
|------|-----------|----------|---------------|-----------|
| **booking.php** | Bukti bayar (image) | ✅ Type: JPG/PNG/GIF<br>✅ Size: max 2MB<br>✅ Preview client-side | `uploads/proofs/` | User upload bukti |
| **BookingController.php** | Bukti bayar | ✅ Server validation<br>✅ Move uploaded file | `uploads/proofs/` | Process upload |
| **admin/dashboard.php** | Gambar mobil (multiple) | ✅ Multiple images | `uploads/cars/` | Admin upload via modal |
| **AdminController.php** | Gambar mobil | ✅ Loop multiple files | `uploads/cars/` | Save to DB |

**Implementasi Upload:**
```php
// Validasi file
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 2 * 1024 * 1024; // 2MB

// Upload file
$filename = uniqid() . '_' . time() . '.' . $ext;
$filepath = $upload_dir . $filename;
move_uploaded_file($file['tmp_name'], $filepath);
```

---

### **4. 🔗 RELASI DATA: 2+ Tabel Terhubung**

| File | Query dengan JOIN | Tabel yang Direlasikan | Jenis Relasi |
|------|-------------------|------------------------|--------------|
| **booking_history.php** | ```sql SELECT b.*, c.car_name, u.name, p.image_path FROM bookings b JOIN cars c ON b.car_id = c.car_id JOIN users u ON b.user_id = u.user_id LEFT JOIN payment_proof p ON b.booking_id = p.booking_id ``` | 4 tabel: bookings, cars, users, payment_proof | Many-to-One |
| **admin/bookings.php** | ```sql SELECT b.*, c.car_name, u.name FROM bookings b JOIN cars c ON b.car_id = c.car_id JOIN users u ON b.user_id = u.user_id ``` | 3 tabel: bookings, cars, users | Many-to-One |
| **cars.php** | ```sql SELECT c.*, (SELECT image_path FROM car_images WHERE car_id = c.car_id LIMIT 1) AS thumb FROM cars c ``` | 2 tabel: cars, car_images | One-to-Many |

**Diagram Relasi:**
```
USERS (1) → (many) BOOKINGS (1) → (1) CARS
                  ↓                     ↓
           PAYMENT_PROOF         CAR_IMAGES
```

---

## **🚀 MENJALANKAN APLIKASI**

### **1. Dengan XAMPP/MAMP**
```
http://localhost/rental_mobil/public/
```

### **2. Dengan PHP Built-in Server**
```bash
cd rental_mobil
php -S localhost:8000 -t public
```
Akses: `http://localhost:8000`

### **3. URL Penting**
| Halaman | URL |
|---------|-----|
| Homepage | `http://localhost/rental_mobil/public/` |
| Daftar Mobil | `http://localhost/rental_mobil/public/cars.php` |
| Login User | `http://localhost/rental_mobil/public/login.php` |
| Register | `http://localhost/rental_mobil/public/register.php` |
| Login Admin | `http://localhost/rental_mobil/public/admin/login.php` |
| Dashboard Admin | `http://localhost/rental_mobil/public/admin/dashboard.php` |

---

## **🔧 TROUBLESHOOTING**

### **Masalah Umum & Solusi**

| Masalah | Penyebab | Solusi |
|---------|----------|--------|
| **Database connection failed** | Config salah / MySQL tidak jalan | Cek db.php, start MySQL service |
| **Upload file gagal** | Permission folder / php.ini setting | `chmod 755 uploads/`, cek upload_max_filesize |
| **Session tidak bekerja** | session_start() tidak dipanggil / output sebelum session | Pastikan session_start() di line pertama |
| **Gambar tidak muncul** | Path salah / file tidak ada | Cek folder uploads, permission |
| **Admin tidak bisa login** | Role bukan 'admin' / password salah | Cek role di DB, gunakan create_admin.php |

### **Cek Konfigurasi PHP**
```bash
# Cek PHP version
php -v

# Cek PHP extensions
php -m | grep -E "mysqli|session|fileinfo"

# Cek php.ini settings
upload_max_filesize = 2M
post_max_size = 8M
max_file_uploads = 20
```

### **Debug Mode**
Akses: `http://localhost/rental_mobil/tes_booking.php`
File ini untuk testing koneksi database, session, dan booking process.

---

## **📈 FITUR YANG SUDAH IMPLEMENT**

| Fitur | Status | File Bukti |
|-------|--------|------------|
| ✅ Autentikasi User & Admin | ✅ | AuthController.php, login.php |
| ✅ Password Hashing (bcrypt) | ✅ | AuthController.php line 39,81 |
| ✅ Session Management | ✅ | Semua file dengan session_start() |
| ✅ CRUD Mobil | ✅ | cars.php, admin/dashboard.php |
| ✅ CRUD Booking | ✅ | booking.php, admin/bookings.php |
| ✅ CRUD Users | ✅ | register.php, AuthController.php |
| ✅ Upload File (gambar) | ✅ | booking.php, AdminController.php |
| ✅ Relasi Database (5 tabel) | ✅ | rental_mobil.sql, booking_history.php |
| ✅ Responsive Design | ✅ | style.css dengan media queries |
| ✅ Admin Dashboard | ✅ | admin/dashboard.php |
| ✅ Search & Filter | ✅ | cars.php dengan form GET |

---
