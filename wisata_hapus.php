<?php
/**
 * AKSI HAPUS DATA WISATA (CRUD - DELETE)
 * Menghapus baris pariwisata berdasarkan ID yang dikirim lewat URL.
 */
session_start();
require_once 'koneksi.php';

// Proteksi akses penjual
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: login.php");
    exit;
}

$id_wisata = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_wisata > 0) {
    try {
        $stmt = $db->prepare("DELETE FROM wisata WHERE id = :id");
        $stmt->execute(['id' => $id_wisata]);
    } catch (PDOException $e) {
        die("Gagal menghapus data pariwisata: " . $e->getMessage());
    }
}

$_SESSION['sukses_pay'] = "Destinasi wisata berhasil dihapus dari inventori.";
header("Location: dashboard_penjual.php");
exit;
?>
