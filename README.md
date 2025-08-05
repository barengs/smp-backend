# Sistem Informasi Manajemen Pesantren (SMP) - Backend API

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<h3 align="center">
🎓 Sistem Informasi Manajemen Pesantren (SMP) - Backend API
</h3>

## Tentang Aplikasi

SMP Backend adalah sistem informasi manajemen pesantren yang dirancang untuk mengelola operasi pesantren secara komprehensif. Aplikasi ini menggunakan Laravel framework dengan fitur-fitur modern untuk mendukung manajemen pesantren yang efisien dan terintegrasi.

### Fitur Utama

#### 🎓 Pendaftaran Santri

-   **Pendaftaran Online**: Sistem pendaftaran santri baru secara online
-   **Verifikasi Data**: Validasi dan verifikasi data santri
-   **Dokumen Digital**: Upload dan manajemen dokumen pendaftaran
-   **Status Tracking**: Tracking status pendaftaran santri

#### 👥 Manajemen Santri

-   **Data Santri**: Manajemen data lengkap santri
-   **Status Santri**: Tracking status (Aktif, Tugas, Alumni)
-   **Riwayat Akademik**: Riwayat pendidikan dan prestasi
-   **Asrama**: Manajemen penempatan asrama

#### 🏦 Bank Santri

-   **Tabungan Santri**: Sistem tabungan untuk santri
-   **Transaksi Keuangan**: Setoran, penarikan, transfer
-   **Laporan Keuangan**: Laporan keuangan santri
-   **Produk Keuangan**: Berbagai produk keuangan untuk santri

#### 📚 Manajemen Pendidikan

-   **Program Studi**: Manajemen program pendidikan
-   **Kelas dan Kelompok**: Pengelolaan kelas dan kelompok belajar
-   **Jadwal Pelajaran**: Penjadwalan kegiatan belajar
-   **Evaluasi**: Sistem evaluasi dan penilaian

#### 🔐 Manajemen Keamanan

-   **Access Control**: Kontrol akses berdasarkan role
-   **User Management**: Manajemen user dan permission
-   **Audit Trail**: Pencatatan aktivitas sistem
-   **Data Protection**: Perlindungan data sensitif

#### 📊 Master Data

-   **Data Wilayah**: Provinsi, kota, kecamatan, desa
-   **Data Pendidikan**: Tingkat pendidikan, jenis pendidikan
-   **Data Pekerjaan**: Profesi dan pekerjaan
-   **Data Akademik**: Tahun akademik, program studi

## Teknologi

-   **Framework**: Laravel 12.x
-   **Database**: MySQL/PostgreSQL
-   **Authentication**: JWT (JSON Web Tokens)
-   **Documentation**: Scramble (OpenAPI 3.0)
-   **API**: RESTful API
-   **Validation**: Laravel Validation
-   **Testing**: PHPUnit

## Instalasi

### Prerequisites

-   PHP 8.2+
-   Composer
-   MySQL/PostgreSQL
-   Node.js & NPM (untuk frontend)

### Setup

1. **Clone Repository**

```bash
git clone <repository-url>
cd smp-backend
```

2. **Install Dependencies**

```bash
composer install
npm install
```

3. **Environment Setup**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Setup**

```bash
php artisan migrate
php artisan db:seed
```

5. **Storage Setup**

```bash
php artisan storage:link
```

6. **Run Application**

```bash
php artisan serve
```

## API Documentation

### 📚 Dokumentasi Lengkap

Dokumentasi API lengkap tersedia di: **`/docs/api`**

Setelah menjalankan aplikasi, buka browser dan akses:

```
http://localhost:8000/docs/api
```

### 🔑 Authentication

Semua endpoint memerlukan token JWT (kecuali login/register):

```bash
# Login untuk mendapatkan token
POST /api/login
{
    "email": "user@example.com",
    "password": "password"
}

# Gunakan token di header
Authorization: Bearer {token}
```

### 📋 Endpoint Groups

#### Authentication

-   `POST /api/login` - Login user
-   `POST /api/register` - Register user baru
-   `GET /api/profile` - Profile user
-   `POST /api/logout` - Logout user

#### Dashboard

-   `GET /api/dashboard` - Dashboard utama pesantren
-   `GET /api/dashboard/statistics` - Statistik transaksi keuangan

#### Registration

-   `GET /api/registration` - Daftar pendaftaran santri
-   `POST /api/registration` - Pendaftaran santri baru
-   `GET /api/registration/{id}` - Detail pendaftaran
-   `PUT /api/registration/{id}` - Update pendaftaran
-   `DELETE /api/registration/{id}` - Hapus pendaftaran

#### Students

-   `GET /api/student` - Daftar santri
-   `POST /api/student` - Tambah santri baru
-   `GET /api/student/{id}` - Detail santri
-   `PUT /api/student/{id}` - Update data santri
-   `DELETE /api/student/{id}` - Hapus data santri
-   `POST /api/student-status` - Update status santri
-   `GET /api/student/program/{programId}` - Santri berdasarkan program

#### Bank Santri

-   `GET /api/product` - Daftar produk keuangan
-   `POST /api/product` - Buat produk keuangan baru
-   `GET /api/product/{id}` - Detail produk keuangan
-   `PUT /api/product/{id}` - Update produk keuangan
-   `DELETE /api/product/{id}` - Hapus produk keuangan
-   `GET /api/product/type/{type}` - Produk berdasarkan tipe
-   `GET /api/product/active` - Produk aktif saja
-   `POST /api/product/{id}/toggle` - Toggle status produk

#### Transactions

-   `GET /api/transaction` - Daftar transaksi keuangan
-   `POST /api/transaction` - Buat transaksi
-   `GET /api/transaction/{id}` - Detail transaksi
-   `PUT /api/transaction/{id}` - Update transaksi
-   `DELETE /api/transaction/{id}` - Hapus transaksi
-   `POST /api/transaction/cash-deposit` - Setoran tunai santri
-   `POST /api/transaction/cash-withdrawal` - Penarikan tunai santri
-   `POST /api/transaction/fund-transfer` - Transfer dana santri
-   `GET /api/transaction/account/{accountNumber}` - Transaksi per rekening
-   `GET /api/transaction/status/{status}` - Transaksi berdasarkan status
-   `GET /api/transaction/date-range` - Transaksi berdasarkan rentang tanggal
-   `POST /api/transaction/{id}/reverse` - Balikkan transaksi
-   `GET /api/transaction/summary` - Ringkasan transaksi

#### Education

-   `GET /api/master/program` - Program studi
-   `GET /api/master/education` - Data pendidikan
-   `GET /api/master/education-class` - Kelas pendidikan
-   `GET /api/master/classroom` - Ruang kelas
-   `GET /api/master/class-group` - Kelompok kelas
-   `GET /api/master/academic-year` - Tahun akademik

#### Security

-   `GET /api/master/role` - Role dan permission
-   `GET /api/master/permission` - Permission system
-   `GET /api/master/menu` - Menu management

#### Master Data

-   `GET /api/region/province` - Data provinsi
-   `GET /api/region/city` - Data kota
-   `GET /api/region/district` - Data kecamatan
-   `GET /api/region/village` - Data desa
-   `GET /api/master/occupation` - Data pekerjaan
-   `GET /api/master/profession` - Data profesi

## Struktur Database

### Tabel Utama

#### Pesantren System

-   `students` - Data santri
-   `employees` - Data asatidz
-   `parent_profiles` - Data orang tua
-   `registrations` - Pendaftaran santri
-   `activities` - Kegiatan pesantren
-   `news` - Berita dan pengumuman

#### Bank Santri System

-   `products` - Produk keuangan santri
-   `accounts` - Rekening santri
-   `transactions` - Transaksi keuangan
-   `transaction_ledger` - Buku besar transaksi
-   `account_movements` - Pergerakan rekening

#### Education System

-   `programs` - Program studi
-   `educations` - Data pendidikan
-   `education_classes` - Kelas pendidikan
-   `classrooms` - Ruang kelas
-   `class_groups` - Kelompok kelas
-   `academic_years` - Tahun akademik

#### Master Data

-   `provinces` - Data provinsi
-   `cities` - Data kota
-   `districts` - Data kecamatan
-   `villages` - Data desa
-   `occupations` - Data pekerjaan
-   `professions` - Data profesi

## Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=StudentTest

# Run with coverage
php artisan test --coverage
```

## Deployment

### Production Setup

1. **Environment Configuration**

```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

2. **Database Migration**

```bash
php artisan migrate --force
```

3. **Cache Configuration**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. **Queue Setup**

```bash
# Start queue worker
php artisan queue:work
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

Untuk bantuan teknis atau pertanyaan, silakan hubungi tim development.

---

**Sistem Informasi Manajemen Pesantren (SMP)** - Built with ❤️ using Laravel
