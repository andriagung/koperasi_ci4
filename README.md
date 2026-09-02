# SERASI (Sistem ERP & Ritel Aplikasi Koperasi)

SERASI adalah aplikasi Sistem Informasi Manajemen Koperasi Terpadu yang dirancang khusus untuk memfasilitasi kebutuhan Koperasi Karyawan. Aplikasi ini dibangun dengan mengintegrasikan fitur Simpan Pinjam, operasional Minimarket (Waserda), serta sistem akuntansi (ERP) dalam satu platform terpusat.

## 🚀 Fitur Utama

### 1. Manajemen Anggota & Kepegawaian
- Pendaftaran anggota baru & pencetakan kartu anggota.
- Integrasi profil karyawan (Divisi, NIP, Kontak).

### 2. Koperasi Simpan Pinjam (KSP)
- **Simpanan:** Pencatatan Simpanan Pokok, Wajib, dan Sukarela. Tarik dan setor simpanan.
- **Pinjaman:** Pengajuan pinjaman, kalkulasi cicilan otomatis, pelunasan sebagian atau penuh.

### 3. Waserda (Warung Serba Ada / Minimarket)
- **Point of Sale (POS):** Sistem kasir digital terintegrasi.
- **Manajemen Inventaris:** Lacak stok barang, peringatan stok menipis.
- **Pembelian (Purchasing):** Integrasi *Purchase Order* (PO) dengan supplier.

### 4. Enterprise Resource Planning (ERP) & Akuntansi
- **Jurnal Otomatis (Auto-Journal):** Setiap transaksi KSP & Waserda otomatis masuk ke dalam jurnal akuntansi.
- **Laporan Keuangan Real-Time:** 
  - Buku Besar (General Ledger)
  - Neraca Saldo (Trial Balance)
  - Laporan Laba Rugi (Income Statement)
  - Laporan Neraca (Balance Sheet)

### 5. Keamanan & Akses
- **Role-Based Access Control (RBAC):** Hak akses terpisah untuk Super Admin, Admin Koperasi, Kasir Waserda, dan Akuntan.
- **Enkripsi URL (Hashids):** URL ID dienkripsi secara *on-the-fly* untuk menghindari *ID-Guessing/Insecure Direct Object Reference (IDOR)*.

## 🛠️ Tech Stack
- **Framework:** CodeIgniter 4 (PHP 8.1+)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap / Tailwind CSS / DataTables
- **Security:** Hashids untuk ID Obfuscation

## 📦 Instalasi (Environment Lokal)

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di komputer lokal:

1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/andriagung/koperasi_ci4.git
   cd koperasi_ci4
   ```

2. **Instal dependensi melalui Composer:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment:**
   - Ubah nama file `env` menjadi `.env`.
   - Atur environment ke mode pengembangan: `CI_ENVIRONMENT = development`.
   - Atur konfigurasi koneksi database Anda di bagian `database.default`.

4. **Migrasi Database:**
   *(Opsional jika Anda sudah memulihkan database dari dump)*
   ```bash
   php spark migrate
   ```

5. **Jalankan Aplikasi:**
   ```bash
   php spark serve --port 8080
   ```
   Akses aplikasi di: `http://localhost:8080`

## 📝 Lisensi
Dikembangkan secara khusus untuk pemenuhan kebutuhan digitalisasi Koperasi Karyawan RSUD 45 Kuningan.
