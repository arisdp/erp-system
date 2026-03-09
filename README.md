# 🚀 ERP SaaS: Integrated Business Management System

> **Solusi ERP berbasis cloud yang ringan, cepat, dan terintegrasi untuk bisnis modern.**

ERP SaaS adalah platform manajemen bisnis terpadu yang dirancang untuk mengelola seluruh aspek operasional perusahaan dalam satu sistem. Dibangun dengan fokus pada efisiensi, keamanan multi-tenant, dan integrasi ekosistem digital.

---

## 🌟 Fitur Utama (Features)

### 💰 1. Modul Keuangan & Akuntansi (Finance)
- **Multi-Currency**: Transaksi mendukung mata uang asing dengan kalkulasi exchange rate otomatis.
- **Journal Engine**: Input jurnal otomatis dari seluruh modul operasional.
- **Full Reporting**: Neraca (Balance Sheet), Laba Rugi (P&L), dan Buku Besar (General Ledger).
- **Export PDF**: Seluruh laporan keuangan dapat diekspor ke PDF format profesional.
- **Manajemen Bank**: Tracking mutasi bank dan rekonsiliasi saldo.

### 👥 2. Sumber Daya Manusia (HR & Payroll)
- **Database Karyawan**: Manajemen data personal, departemen, dan posisi.
- **Manajemen Kehadiran**: Pencatatan absensi, lembur, dan keterlambatan.
- **Penggajian (Payroll)**: Kalkulasi gaji otomatis termasuk uang makan, lembur, dan potongan cuti.
- **Manajemen Cuti**: Alur pengajuan dan approval cuti bertingkat.

### 📦 3. Inventaris & Gudang (Inventory)
- **Multi-Warehouse**: Pelacakan stok di berbagai lokasi gudang.
- **Batch Tracking**: Penomoran batch otomatis pada setiap penerimaan barang.
- **QR Code Labeling**: Cetak label QR Code untuk pelacakan fisik barang.
- **Kartu Stok**: Riwayat mutasi barang secara real-time.
- **Min/Max Alert**: Peringatan otomatis jika stok berada di level kritis.

### 🛒 4. Pembelian & Penjualan (Procurement & Sales)
- **Approval Workflow**: Semua dokumen SO/PO melalui sistem persetujuan formal dan notifikasi email.
- **Marketplace Integration**: Dukungan biaya platform, diskon, dan voucher marketplace (Shopee, Tokopedia, dll).
- **Automated Invoicing**: Generate faktur otomatis dari surat jalan atau penerimaan barang.
- **Webhook Ready**: Endpoint untuk integrasi pesanan real-time dari pihak ketiga.

### 🔗 5. Ekosistem Terintegrasi (API Layer)
- **REST API Middleware**: Jalur API terlindungi token untuk koneksi ke Mobile App.
- **Audit Trails**: Mencatat setiap aktivitas user dan perubahan data (siapa, kapan, apa).
- **Queue System**: Pemrosesan berat (seperti depresiasi aset) berjalan di background.

---

## 🛠️ Stack Teknologi (Tech Stack)

| Layer | Teknologi |
|---|---|
| **Programming** | PHP 8.1+ |
| **Framework** | Laravel 10.x LTS |
| **Database** | PostgreSQL 15+ |
| **Frontend UI** | AdminLTE 3 + Bootstrap 4 |
| **Libraries** | jQuery, DataTables, Chart.js, DomPDF, QRCode.js |
| **Server Side** | Laravel Queue, Task Scheduling |

---

## 🚀 Instalasi & Persiapan (Installation)

### 1. Requirements
- PHP >= 8.1
- Composer
- PostgreSQL DB

### 2. Setup Langkah-demi-Langkah
```bash
# Clone repository
git clone [url-repo]

# Install dependencies
composer install
npm install && npm run build

# Setup Environment
cp .env.example .env
php artisan key:generate

# Migrasi & Data Demo
php artisan migrate --seed
php artisan install:api

# Jalankan Server
php artisan serve
```

### 3. Menjalankan Background Worker
Untuk fitur Email Notifikasi dan Depresiasi Aset:
```bash
php artisan queue:work
```

---

## 🔑 Akun Demo (Default Login)
- **URL**: `http://localhost:8000`
- **Email**: `admin@erp.test`
- **Password**: `password`

---

## 📜 Lisensi (License)
Proyek ini dikembangkan sebagai sistem internal perusahaan (Proprietary).

---
*Dibuat oleh Team - Maret 2026*
