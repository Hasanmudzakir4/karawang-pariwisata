# SISTEM INFORMASI PARIWISATA KABUPATEN KARAWANG
Sistem Marketplace Tiket Wisata Karawang berbasis PHP Native & MySQL.

---

## 1. ENTITY RELATIONSHIP DIAGRAM (ERD)

```text
  ┌────────────────┐               ┌────────────────┐
  │     USERS      │               │     WISATA     │
  ├────────────────┤               ├────────────────┤
  │ PK  id         │ 1           * │ PK  id         │
  │     nama       ├──────────────>│ FK  id_penjual │
  │     email      │               │     nama_wisata│
  │     password   │               │     gambar     │
  │     role       │               │     deskripsi  │
  └───────┬────────┘               │     lokasi     │
          │                        │     harga_tiket│
          │ 1                      │     stok_tiket │
          │                        │  jam_operasional│
          │                        └───────┬────────┘
          │                                │
          │                                │ 1
          │ 1                              │
          ▼                                ▼
  ┌─────────────────────────────────────────────────┐
  │                   TRANSAKSI                     │
  ├─────────────────────────────────────────────────┤
  │ PK  id                                          │
  │ FK  id_user  (Mengaitkan ke USERS.id)           │
  │ FK  id_wisata (Mengaitkan ke WISATA.id)         │
  │     jumlah_tiket                              │
  │     total_harga                                 │
  │     tanggal_transaksi                           │
  │     status_pembayaran ('belum_bayar','lunas','batal')
  └───────────────────────┬─────────────────────────┘
                          │ 1
                          │
                          │ 1
                          ▼
                ┌──────────────────┐
                │      TIKET       │
                ├──────────────────┤
                │ PK  id           │
                │ FK  id_transaksi │ (Mengaitkan ke TRANSAKSI.id)
                │     kode_tiket   │
                │  tanggal_kunjungan
                └──────────────────┘
```

**Penjelasan Relasi:**
- **USERS ke WISATA (1 to Many):** Satu pengelola (role: penjual) dapat mendaftarkan/mengelola banyak destinasi wisata.
- **USERS ke TRANSAKSI (1 to Many):** Satu wisatawan (role: pembeli) dapat membuat banyak transaksi pemesanan tiket.
- **WISATA ke TRANSAKSI (1 to Many):** Satu destinasi wisata dapat dipesan dalam banyak transaksi berbeda.
- **TRANSAKSI ke TIKET (1 to 1):** Setiap satu transaksi sukses/aktif mereferensikan tepat satu lembar tiket digital utama yang memiliki kode tiket unik dan tanggal kunjungan terpilih.

---

## 2. FLOWCHART SISTEM (Alur Pembelian Tiket Wisata)

```text
[ Pengunjung ] ---> Mengakses Landing Page (index.php)
                         │
                         ▼
             Memilih Destinasi Wisata
                         │
                         ▼
             Membaca Detail Wisata (detail.php)
                         │
                         ▼
             Klik Tombol "Pesan Tiket"
                         │
               ┌─────────┴─────────┐
               ▲                   ▼
       [ Belum Login ]        [ Sudah Login ]
               │                   │
               ▼                   ▼
    Tampil Alert & diarahkan    Diarahkan ke Checkout (pesan.php)
    ke halaman login.php           │
                                   ▼
                            Isi Form Pemesanan:
                            - Jumlah Tiket
                            - Tanggal Kunjungan
                                   │
                                   ▼
                            Validasi Stok & Input
                                   │
                     ┌─────────────┴─────────────┐
                     ▼ (Stok Cukup)              ▼ (Stok Kurang)
             Buat Transaksi Baru             Tampil Error &
             (Status: belum_bayar)           Gagal Checkout
                     │
                     ▼
             Diarahkan ke Dashboard Wisatawan
             - Transfer Pembayaran
             - Klik "Konfirmasi Bayar"
                     │
                     ▼
             Sistem mengupdate slot tiket
             & mengubah status menjadi [LUNAS]
                     │
                     ▼
             Tampil Tiket Digital Resmi (TICK-xxxxxx)
             dengan Status Terverifikasi (Dapat Dicetak)
```

---

## 3. STRUKTUR FOLDER PROJECT (PHP Native & MySQL)

Sistem Informasi ini disusun dengan struktur folder modular dan bersih untuk mempermudahkan pemeliharaan kode:

```text
pariwisata-karawang/
│
├── assets/                 # Folder Penyimpanan File Statis
│   ├── css/                # Custom Stylesheet (Bootstrap 5)
│   ├── js/                 # Custom JavaScript
│   └── uploads/            # Direktori Gambar Wisata yang Diunggah
│
├── database.sql            # File MySQL Import Schema & Seed Data
│
├── koneksi.php             # Script Koneksi PHP PDO / mysqli ke MySQL
│
├── index.php               # Halaman Utama Wisatawan (Daftar Wisata Umum)
├── detail.php              # Halaman Detail Wisata (Fasilitas, Harga, dll)
├── login.php               # Form Login Multi-role
├── register.php            # Form Pendaftaran Pembeli Baru
├── logout.php              # Proses Penghapusan Session
│
├── pesan.php               # Logika Checkout Pemesanan Tiket
├── bayar.php               # Logika Simulasi Verifikasi Pembayaran Transaksi
├── tiket_digital.php       # Tampilan & Print out Tiket Resmi Wisatawan
│
├── dashboard_pembeli.php   # Dashboard Wisatawan (Riwayat Transaksi & Tiket)
├── dashboard_penjual.php   # Dashboard Pengelola Wisata (Input Wisata, Laporan Penjualan)
│
├── wisata_tambah.php       # Aksi CRUD: Formulir Tambah Wisata (Upload File)
├── wisata_edit.php         # Aksi CRUD: Formulir Edit Wisata (Ganti File Gambar)
└── wisata_hapus.php        # Aksi CRUD: Eksekusi Penghapusan Destinasi Wisata
```

---

## 4. CARA MENJALANKAN DI XAMPP

1. **Persiapan Folder:**
   - Salin semua file dari folder `/xampp-php/` di project ini.
   - Buat folder baru bernama `pariwisata-karawang` di dalam direktori `C:/xampp/htdocs/`.
   - Tempel (*Paste*) semua file di dalam folder tersebut.

2. **Membuat Database:**
   - Buka XAMPP Control Panel, aktifkan modul **Apache** dan **MySQL**.
   - Buka browser Anda, akses: `http://localhost/phpmyadmin/`.
   - Buat database baru bernama `pariwisata_karawang`.
   - Pilih tab **Import**, klik **Browse...**, pilih file `database.sql` yang berada di dalam folder project Anda, lalu tekan tombol **Import / Go**.

3. **Menjalankan Aplikasi:**
   - Buka tab browser baru dan ketik: `http://localhost/pariwisata-karawang/`.
   - Nikmati aplikasi pariwisata Kabupaten Karawang!

4. **Kredensial Default Login Cepat:**
   - **Sebagai Pengelola Wisata:**
     - Email: `penjual@karawang.go.id`
     - Password: `password123`
   - **Sebagai Wisatawan / Pembeli:**
     - Email: `user@gmail.com`
     - Password: `password123`
