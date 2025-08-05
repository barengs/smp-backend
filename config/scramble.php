<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Your API path. By default, all routes starting with this path will be added to the docs.
     * If you need to change this behavior, you can add your custom routes resolver using `Scramble::routes()`.
     */
    'api_path' => 'api',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    /*
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api.json',

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '1.0.0'),

        /*
         * Description rendered on the home page of the API documentation (`/docs/api`).
         */
        'description' => '
# Sistem Informasi Manajemen Pesantren (SMP) - API Documentation

## Tentang Aplikasi

SMP adalah sistem informasi manajemen pesantren yang dirancang untuk mengelola operasi pesantren secara komprehensif. Aplikasi ini mencakup semua aspek manajemen pesantren modern dari pendaftaran santri hingga manajemen keuangan.

## Fitur Utama

### 🎓 Pendaftaran Santri
- **Pendaftaran Online**: Sistem pendaftaran santri baru secara online
- **Verifikasi Data**: Validasi dan verifikasi data santri
- **Dokumen Digital**: Upload dan manajemen dokumen pendaftaran
- **Status Tracking**: Tracking status pendaftaran santri

### 👥 Manajemen Santri
- **Data Santri**: Manajemen data lengkap santri
- **Status Santri**: Tracking status (Aktif, Tugas, Alumni)
- **Riwayat Akademik**: Riwayat pendidikan dan prestasi
- **Asrama**: Manajemen penempatan asrama

### 🏦 Bank Santri
- **Tabungan Santri**: Sistem tabungan untuk santri
- **Transaksi Keuangan**: Setoran, penarikan, transfer
- **Laporan Keuangan**: Laporan keuangan santri
- **Produk Keuangan**: Berbagai produk keuangan untuk santri

### 📚 Manajemen Pendidikan
- **Program Studi**: Manajemen program pendidikan
- **Kelas dan Kelompok**: Pengelolaan kelas dan kelompok belajar
- **Jadwal Pelajaran**: Penjadwalan kegiatan belajar
- **Evaluasi**: Sistem evaluasi dan penilaian

### 🔐 Manajemen Keamanan
- **Access Control**: Kontrol akses berdasarkan role
- **User Management**: Manajemen user dan permission
- **Audit Trail**: Pencatatan aktivitas sistem
- **Data Protection**: Perlindungan data sensitif

### 📊 Master Data
- **Data Wilayah**: Provinsi, kota, kecamatan, desa
- **Data Pendidikan**: Tingkat pendidikan, jenis pendidikan
- **Data Pekerjaan**: Profesi dan pekerjaan
- **Data Akademik**: Tahun akademik, program studi

## Teknologi

- **Framework**: Laravel 12.x
- **Database**: MySQL/PostgreSQL
- **Authentication**: JWT (JSON Web Tokens)
- **Documentation**: Scramble (OpenAPI 3.0)
- **API**: RESTful API
- **Validation**: Laravel Validation
- **Testing**: PHPUnit

## Penggunaan

1. **Authentication**: Semua endpoint memerlukan token JWT (kecuali login/register)
2. **Headers**: Gunakan `Authorization: Bearer {token}` untuk request
3. **Response Format**: Semua response menggunakan format JSON standar
4. **Error Handling**: Error response dengan kode status HTTP yang sesuai

## Endpoint Groups

- **Authentication**: Login, register, profile management
- **Dashboard**: Data ringkasan dan statistik pesantren
- **Registration**: Pendaftaran santri baru
- **Students**: Manajemen data santri
- **Bank**: Operasi keuangan santri
- **Education**: Manajemen pendidikan
- **Security**: Manajemen keamanan dan user
- **Master Data**: Data master (wilayah, pendidikan, dll)

## Support

Untuk bantuan teknis atau pertanyaan, silakan hubungi tim development.
        ',
    ],

    /*
     * Customize Stoplight Elements UI
     */
    'ui' => [
        /*
         * Define the title of the documentation's website. App name is used when this config is `null`.
         */
        'title' => 'Sistem Informasi Manajemen Pesantren (SMP) - API Documentation',

        /*
         * Define the theme of the documentation. Available options are `light` and `dark`.
         */
        'theme' => 'light',

        /*
         * Hide the `Try It` feature. Enabled by default.
         */
        'hide_try_it' => false,

        /*
         * Hide the schemas in the Table of Contents. Enabled by default.
         */
        'hide_schemas' => false,

        /*
         * URL to an image that displays as a small square logo next to the title, above the table of contents.
         */
        'logo' => '',

        /*
         * Use to fetch the credential policy for the Try It feature. Options are: omit, include (default), and same-origin
         */
        'try_it_credentials_policy' => 'include',

        /*
         * There are three layouts for Elements:
         * - sidebar - (Elements default) Three-column design with a sidebar that can be resized.
         * - responsive - Like sidebar, except at small screen sizes it collapses the sidebar into a drawer that can be toggled open.
         * - stacked - Everything in a single column, making integrations with existing websites that have their own sidebar or other columns already.
         */
        'layout' => 'responsive',
    ],

    /*
     * The list of servers of the API. By default, when `null`, server URL will be created from
     * `scramble.api_path` and `scramble.api_domain` config variables. When providing an array, you
     * will need to specify the local server URL manually (if needed).
     *
     * Example of non-default config (final URLs are generated using Laravel `url` helper):
     *
     * ```php
     * 'servers' => [
     *     'Live' => 'api',
     *     'Prod' => 'https://scramble.dedoc.co/api',
     * ],
     * ```
     */
    'servers' => [
        'Local Development' => 'http://localhost:8000/api',
        'Production' => 'https://your-domain.com/api',
    ],

    /**
     * Determines how Scramble stores the descriptions of enum cases.
     * Available options:
     * - 'description' – Case descriptions are stored as the enum schema's description using table formatting.
     * - 'extension' – Case descriptions are stored in the `x-enumDescriptions` enum schema extension.
     *
     *    @see https://redocly.com/docs-legacy/api-reference-docs/specification-extensions/x-enum-descriptions
     * - false - Case descriptions are ignored.
     */
    'enum_cases_description_strategy' => 'description',

    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],

    'extensions' => [],
];
