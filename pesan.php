<?php

/**
 * AKSI PROSES CHECKOUT PEMESANAN TIKET WISATA
 * Memotong stok wisata dan mendaftarkan nominal transaksi serta tiket digital.
 */
session_start();
require_once 'config/koneksi.php';

// Proteksi akses login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$id_wisata = isset($_POST['id_wisata']) ? intval($_POST['id_wisata']) : 0;
$jumlah_tiket = isset($_POST['jumlah_tiket']) ? intval($_POST['jumlah_tiket']) : 0;
$tanggal_kunjungan = isset($_POST['tanggal_kunjungan']) ? trim($_POST['tanggal_kunjungan']) : '';

if ($id_wisata === 0 || $jumlah_tiket <= 0 || empty($tanggal_kunjungan)) {
    die("Formulir pemesanan tidak lengkap.");
}

try {
    // Mulai Transaksi Database (Atomic Transaction)
    $db->beginTransaction();

    // 1. Ambil data wisata dengan lock/for update untuk akurasi stock
    $stmt_wisata = $db->prepare("SELECT * FROM wisata WHERE id = :id FOR UPDATE");
    $stmt_wisata->execute(['id' => $id_wisata]);
    $wisata = $stmt_wisata->fetch();

    if (!$wisata) {
        throw new Exception("Destinasi wisata yang dipilih tidak ditemukan.");
    }

    if ($wisata['stok_tiket'] < $jumlah_tiket) {
        throw new Exception("Maaf, stok tiket tidak mencukupi. Sisa stok tersedia: " . $wisata['stok_tiket'] . " tiket.");
    }

    // 2. Hitung total harga
    $total_harga = $wisata['harga_tiket'] * $jumlah_tiket;

    // 3. Potong stok tiket
    $stok_baru = $wisata['stok_tiket'] - $jumlah_tiket;
    $stmt_update_stok = $db->prepare("UPDATE wisata SET stok_tiket = :stok WHERE id = :id");
    $stmt_update_stok->execute(['stok' => $stok_baru, 'id' => $id_wisata]);

    // 4. Daftarkan baris Transaksi baru (Default status_pembayaran: belum_bayar)
    $stmt_insert_tx = $db->prepare("INSERT INTO transaksi (id_user, id_wisata, jumlah_tiket, total_harga, status_pembayaran) VALUES (:id_user, :id_wisata, :jumlah_tiket, :total_harga, 'belum_bayar')");
    $stmt_insert_tx->execute([
        'id_user' => $id_user,
        'id_wisata' => $id_wisata,
        'jumlah_tiket' => $jumlah_tiket,
        'total_harga' => $total_harga
    ]);

    // Ambil ID transaksi yang baru dibuat
    $id_transaksi = $db->lastInsertId();

    // 5. Buat kode tiket digital unik (Format TICK-xxxxxx)
    $kode_tiket = 'TICK-' . rand(100000, 999999);
    $stmt_insert_tiket = $db->prepare("INSERT INTO tiket (id_transaksi, kode_tiket, tanggal_kunjungan) VALUES (:id_transaksi, :kode_tiket, :tanggal_kunjungan)");
    $stmt_insert_tiket->execute([
        'id_transaksi' => $id_transaksi,
        'kode_tiket' => $kode_tiket,
        'tanggal_kunjungan' => $tanggal_kunjungan
    ]);

    // Commit transaksi jika sukses
    $db->commit();

    $_SESSION['sukses_order'] = "Pemesanan tiket berhasil didaftarkan! Silakan selesaikan pembayaran nominal Rp " . number_format($total_harga, 0, ',', '.') . " untuk mengunduh tiket digital Anda.";
    header("Location: dashboard_pembeli.php");
    exit;
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    // Tampilkan pesan error dan tawarkan tombol kembali
?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <title>Kesalahan Pemesanan - Pariwisata Karawang</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body class="bg-light">
        <div class="container py-5 text-center">
            <div class="card p-5 shadow-sm mx-auto" style="max-width: 500px; border-radius: 20px;">
                <h3 class="text-danger fw-bold mb-3">Pesanan Tiket Gagal!</h3>
                <p class="text-muted mb-4"><?= $e->getMessage() ?></p>
                <a href="detail.php?id=<?= $id_wisata ?>" class="btn btn-primary bg-success border-0 px-4 py-2 rounded-3">Kembali & Coba Lagi</a>
            </div>
        </div>
    </body>

    </html>
<?php
    exit;
}
?>