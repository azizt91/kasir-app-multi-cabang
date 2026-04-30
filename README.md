# Kasir App - Aplikasi Kasir Berbasis Web (Multi-Cabang & Multi-Gudang)

Kasir App adalah aplikasi Point of Sale (POS) berbasis web yang modern dan ramah pengguna, kini ditingkatkan dengan kapabilitas **Enterprise-Grade** seperti arsitektur **Multi-Cabang**, **Multi-Gudang**, **Sistem Shift Kasir**, dan **Identity Override**. Dirancang khusus untuk membantu pemilik bisnis mengelola transaksi penjualan, memantau pergerakan stok, dan melakukan audit keuangan di berbagai lokasi sekaligus secara terpusat dengan aman.

## Fitur Unggulan Utama

### 🏢 Arsitektur Multi-Cabang & Multi-Gudang
- **Global View & Cabang Scope:** Superadmin dapat memantau seluruh cabang melalui "Global View" atau memfilter data spesifik per cabang.
- **Isolasi Data (Data Privacy):** Admin Cabang dan Kasir secara otomatis dibatasi (scoped) hanya dapat melihat dan mengelola data (pengguna, transaksi, kas, produk) di cabang mereka sendiri.
- **Manajemen Multi-Gudang:** Setiap cabang dapat memiliki lebih dari satu gudang (contoh: Gudang Depan, Gudang Belakang).
- **Mutasi Stok Akurat:** Stok produk dihitung berdasarkan pivot lokasi (tidak lagi terpusat pada satu tabel).

### 📦 Sistem Transfer Stok & Approval
- **Request Transfer:** Admin Cabang dapat mengajukan permintaan transfer stok (antar gudang internal atau antar cabang).
- **Superadmin Approval Workflow:** Demi keamanan aset, perpindahan stok akan berstatus "Pending" dan membutuhkan persetujuan (Approve/Reject) langsung dari Superadmin.
- **Otomatisasi Mutasi:** Stok akan otomatis terpotong di gudang asal dan bertambah di gudang tujuan saat disetujui, tercatat rapi di Riwayat Mutasi.

### 🧾 Identity Override (Kustomisasi Struk per Cabang)
- Setiap cabang dapat memiliki **identitas struk yang berbeda** (Logo, Alamat, Telepon, dan Footer Struk).
- Jika pengaturan spesifik cabang tidak diisi, sistem secara cerdas akan melakukan fallback menggunakan **Pengaturan Pusat (Global)**.
- Dukungan lebar kertas kustomisasi per cabang (58mm atau 80mm).
- **Live Preview Struk:** Admin dapat melihat perubahan desain struk secara real-time langsung di dalam dashboard.

### ⏱️ Sistem Shift & Audit Kasir (Cash Drawer Management)
- **Open/Close Shift:** Kasir wajib membuka laci (memasukkan modal awal) saat mulai bekerja, dan menutup shift saat selesai.
- **Pencatatan Selisih Kas:** Sistem otomatis menghitung total uang yang seharusnya ada di laci vs uang fisik yang diinput kasir.
- **Approval Selisih Kas (Audit):** Jika kasir melaporkan adanya selisih kas (kurang/lebih), laporan akan masuk ke antrean Superadmin untuk diverifikasi dan disetujui sebelum dicatat sebagai pengeluaran/pemasukan sah.
- **Laporan Shift:** Pelacakan akurat performa penjualan per kasir berdasarkan waktu kerjanya.

### 🖨️ Dukungan Cetak Struk & Label
- Mendukung konektivitas Thermal Printer via **Bluetooth**, **WebUSB**, dan dialog **Browser Default**.
- Cetak label barcode otomatis untuk manajemen inventori.

### 📊 Dashboard & Analitik Pintar
- Analisis data penjualan, produk terlaris, notifikasi stok menipis.
- **Widget Ringkasan Saldo Per Cabang:** Superadmin dapat memonitor likuiditas seluruh unit bisnis dalam satu tabel terpusat.
- Grafik laporan keuangan otomatis.

## Panduan Printer

Aplikasi ini mendukung pencetakan struk menggunakan Printer Thermal USB (WebUSB), Bluetooth, dan Browser Dialog.

**Persyaratan WebUSB / Bluetooth API:**
1.  **Browser:** Google Chrome, Microsoft Edge, atau Opera.
2.  **HTTPS:** Wajib menggunakan protokol **https://** atau **http://localhost**.

## Teknologi

- **Backend:** Laravel 11
- **Frontend:** Blade, Tailwind CSS, Alpine.js, Chart.js
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

| Role | Nama | Email | Branch | Hak Akses Utama |
|------|------|-------|--------|----------------|
| **Superadmin** | Superadmin | `admin@minimarket.com` | Global (All) | Akses mutlak, Setting Pusat, Global View, Approval Stok & Kas. |
| **Admin** | Admin Pusat | `admin.pusat@minimarket.com` | Cabang Utama | Manajemen Cabang Utama, Request Stok, Kelola User Cabang. |
| **Kasir** | Kasir Pusat | `kasir1@minimarket.com` | Cabang Utama | Transaksi POS Cabang Utama, Manajemen Shift. |
| **Kasir** | Kasir Bandung | `kasir2@minimarket.com` | Cabang Bandung | Transaksi POS Cabang Bandung, Manajemen Shift. |

## Catatan Arsitektur Multi-Tier

- **Stok Pivot:** Stok tidak lagi disimpan di tabel `products`, melainkan di tabel pivot `product_warehouse` untuk mendukung presisi lokasi stok.
- **Security Scopes:** Query data dilindungi oleh global scope (`BranchScope`) dan autorisasi kontrol (*Gates/Policies*) yang memastikan Admin Cabang tidak dapat "mengintip" atau memanipulasi entitas di luar cabangnya.

---

© 2026 Kasir App - Enterprise Multi-Branch Edition
