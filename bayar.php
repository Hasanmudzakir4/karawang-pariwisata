<?php

session_start();
require_once 'config/koneksi.php';

// Proteksi akses login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id_transaksi === 0 || !in_array($action, ['lunas', 'batal'])) {
    die("Aksi pembayaran tidak valid.");
}

try {
    // Mulai Transaksi Database
    $db->beginTransaction();

    // Ambil data transaksi
    $stmt_tx = $db->prepare("SELECT * FROM transaksi WHERE id = :id FOR UPDATE");
    $stmt_tx->execute(['id' => $id_transaksi]);
    $transaksi = $stmt_tx->fetch();

    if (!$transaksi) {
        throw new Exception("Transaksi tidak ditemukan.");
    }

    // Hak akses pengubahan: pembeli hanya bisa update transaksinya sendiri, penjual bisa siapa saja
    if ($_SESSION['role'] !== 'penjual' && $transaksi['id_user'] !== $id_user) {
        throw new Exception("Anda tidak memiliki hak akses untuk mengubah transaksi ini.");
    }

    if ($action === 'batal' && $transaksi['status_pembayaran'] !== 'batal') {
        // Refund stok tiket
        $stmt_wisata = $db->prepare("SELECT * FROM wisata WHERE id = :id FOR UPDATE");
        $stmt_wisata->execute(['id' => $transaksi['id_wisata']]);
        $wisata = $stmt_wisata->fetch();

        if ($wisata) {
            $stok_kembali = $wisata['stok_tiket'] + $transaksi['jumlah_tiket'];
            $stmt_refund = $db->prepare("UPDATE wisata SET stok_tiket = :stok WHERE id = :id");
            $stmt_refund->execute(['stok' => $stok_kembali, 'id' => $transaksi['id_wisata']]);
        }
    }

    // Update status transaksi
    $stmt_update_tx = $db->prepare("UPDATE transaksi SET status_pembayaran = :status WHERE id = :id");
    $stmt_update_tx->execute(['status' => $action, 'id' => $id_transaksi]);

    $db->commit();

    $_SESSION['sukses_pay'] = "Status transaksi berhasil diperbarui menjadi: " . strtoupper($action);

    // Lempar kembali ke dashboard masing-masing role
    if ($_SESSION['role'] === 'penjual') {
        header("Location: dashboard_penjual.php#laporan");
    } else {
        header("Location: dashboard_pembeli.php");
    }
    exit;
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("Kesalahan pembayaran: " . $e->getMessage());
}
