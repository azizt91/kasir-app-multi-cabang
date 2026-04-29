# Kasir App - Aplikasi Kasir Berbasis Web

Kasir App adalah aplikasi Point of Sale (POS) berbasis web yang modern dan ramah pengguna, dirancang untuk membantu mengelola transaksi penjualan, produk, dan stok dengan efisien.

## Fitur Utama

- **Sistem Kasir (POS):** Antarmuka kasir yang cepat dan mudah digunakan
- **Manajemen Produk & Kategori:** Kelola produk dengan mudah
- **Produk Varian:** Dukungan untuk produk dengan varian (Warna/Ukuran) dengan harga dan stok berbeda
- **Multi Metode Pembayaran:** Tunai, Utang, Kartu, E-Wallet, Transfer
- **Manajemen Piutang:** Pencatatan nama customer dan tandai lunas
- **Laporan:** Penjualan, stok, produk, dan piutang
- **Cetak Struk & Barcode:** Cetak struk transaksi dan label barcode
- **Multi User:** Admin dan Kasir dengan hak akses berbeda
- **Manajemen Supplier:** Kelola data supplier dan kontak
- **Manajemen Pelanggan:** Database pelanggan untuk layanan personal
- **Pembelian Stok (Restok):** Catat pembelian barang masuk dan update stok otomatis
- **Biaya Operasional:** Catat pengeluaran toko (listrik, gaji, dll)
- **Riwayat Transaksi:** Lihat dan kelola histori penjualan (termasuk fitur Void/Batal)
- **Support Printer:** Cetak struk via USB (WebUSB), Bluetooth, dan Browser Dialog
- **Pengaturan Toko:** Nama toko, alamat, logo, dan lainnya

## Panduan Printer USB

Aplikasi ini mendukung pencetakan struk menggunakan Printer Thermal USB (ESC/POS) secara langsung dari browser menggunakan teknologi **WebUSB**.

**Persyaratan:**
1.  **Browser:** Gunakan Google Chrome, Microsoft Edge, atau Opera (Chromium-based).
2.  **Koneksi:** Pastikan printer USB terhubung dan menyala.
3.  **HTTPS:** Fitur WebUSB *hanya* berfungsi pada protokol **https://** atau **http://localhost**. Jika di-hosting di server publik (non-localhost), wajib menggunakan SSL/HTTPS.

**Cara Menggunakan:**
1.  Masuk ke menu **Pengaturan (Settings)**.
2.  Gulir ke bagian **Printer Struk**.
3.  Klik tombol **Connect USB Printer**.
4.  Pilih printer thermal Anda dari daftar popup browser, lalu klik **Connect**.
5.  Status akan berubah menjadi "Terhubung".
6.  Gunakan tombol **Test Print USB** untuk mencoba mencetak.

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

### 1. Extract File

Extract file `kasir-app.zip` ke folder htdocs (XAMPP) atau www (Laragon):

```
C:\xampp\htdocs\kasir-app\
```

### 2. Install Dependencies

Buka terminal/command prompt, masuk ke folder project:

```bash
cd C:\xampp\htdocs\kasir-app
composer install
npm install
```

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasir_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate App Key

```bash
php artisan key:generate
```

### 5. Migrasi Database

Buat database baru di phpMyAdmin dengan nama `kasir_db`, lalu jalankan:

```bash
php artisan migrate --seed
```

### 6. Build Assets

```bash
npm run build
```

### 7. Jalankan Aplikasi

```bash
php artisan serve
```

Buka browser dan akses: `http://127.0.0.1:8000`

## Akun Demo

| Role  | Email                    | Password |
|-------|--------------------------|----------|
| Admin | admin@minimarket.com     | password |
| Kasir | kasir1@minimarket.com    | password |
| Kasir | kasir2@minimarket.com    | password |

## Catatan Penting

- Pastikan XAMPP/Laragon sudah running (Apache & MySQL)
- Jika menggunakan XAMPP, bisa langsung akses via `http://localhost/kasir-app/public`
- Untuk production, arahkan document root ke folder `public/`

---

© 2025 Kasir App
