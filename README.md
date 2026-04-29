# Kasir App - Aplikasi Kasir Berbasis Web (Multi-Cabang & Multi-Gudang)

Kasir App adalah aplikasi Point of Sale (POS) berbasis web yang modern dan ramah pengguna, kini ditingkatkan dengan arsitektur **Multi-Cabang** dan **Multi-Gudang**. Dirancang untuk membantu mengelola transaksi penjualan, produk, dan stok di berbagai lokasi sekaligus dengan efisien.

## Fitur Utama

- **Multi-Cabang (Multi-Branch):** Kelola banyak outlet/cabang dalam satu sistem.
- **Multi-Gudang (Multi-Warehouse):** Kelola stok di berbagai lokasi gudang per cabang.
- **Transfer Stok Internal:** Pindahkan stok antar gudang dengan pencatatan mutasi yang akurat.
- **Sistem Kasir (POS):** Antarmuka kasir cepat yang terisolasi per cabang (Kasir hanya melihat stok cabangnya sendiri).
- **Manajemen Produk & Kategori:** Kelola produk tunggal maupun varian.
- **Produk Varian:** Dukungan untuk produk dengan varian (Warna/Ukuran) dengan harga dan stok berbeda di tiap gudang.
- **Multi Metode Pembayaran:** Tunai, Utang, Kartu, E-Wallet, Transfer.
- **Manajemen Piutang:** Pencatatan nama customer dan pelacakan status pembayaran.
- **Laporan Komprehensif:** Laporan penjualan, stok, produk, dan keuangan yang bisa difilter per cabang.
- **Cetak Struk & Barcode:** Dukungan Thermal Printer (USB, Bluetooth, Browser) dan cetak label barcode.
- **Hak Akses User:** Role Superadmin (Global), Admin Cabang, dan Kasir dengan izin akses spesifik.
- **Manajemen Operasional:** Kelola Pembelian (Restok) ke gudang tertentu dan catat Biaya Operasional per cabang.

## Panduan Printer

Aplikasi ini mendukung pencetakan struk menggunakan Printer Thermal USB (WebUSB), Bluetooth, dan Browser Dialog.

**Persyaratan WebUSB:**
1.  **Browser:** Google Chrome, Microsoft Edge, atau Opera.
2.  **HTTPS:** Wajib menggunakan protokol **https://** atau **http://localhost**.

## Teknologi

- **Backend:** Laravel 11
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Database:** MySQL / MariaDB

## Prasyarat

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / MariaDB

## Instalasi

### 1. Extract Project

Extract file project ke folder server Anda (misal: `C:\xampp\htdocs\kasir-app-multi-cabang\`).

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

Salin `.env.example` menjadi `.env` dan sesuaikan konfigurasi database:

```env
DB_DATABASE=kasir_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup Database & Assets

```bash
php artisan key:generate
php artisan migrate --seed
npm run build
```

### 5. Jalankan Aplikasi

```bash
php artisan serve
```

Akses via browser: `http://127.0.0.1:8000`

## Akun Demo

Sistem menggunakan data awal berbasis skenario untuk pengujian multi-cabang:

| Role | Nama | Email | Branch | Password |
|------|------|-------|--------|----------|
| **Superadmin** | Superadmin | `admin@minimarket.com` | Global (All) | `password` |
| **Admin** | Admin Pusat | `admin.pusat@minimarket.com` | Cabang Utama | `password` |
| **Kasir** | Kasir Pusat | `kasir1@minimarket.com` | Cabang Utama | `password` |
| **Kasir** | Kasir Bandung | `kasir2@minimarket.com` | Cabang Bandung | `password` |

## Catatan Arsitektur

- **Stok:** Stok tidak lagi disimpan di tabel `products`, melainkan di tabel pivot `product_warehouse` untuk mendukung multi-lokasi.
- **Scope:** Data transaksi dan keuangan difilter secara otomatis berdasarkan cabang user yang sedang login (kecuali Superadmin).

---

© 2025 Kasir App - Multi-Branch Edition
