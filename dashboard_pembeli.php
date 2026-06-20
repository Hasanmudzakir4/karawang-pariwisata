<?php

session_start();
require_once 'config/koneksi.php';

// Proteksi akses pembeli
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

try {
    // Ambil histori transaksi dan detail tiket wisatawan
    $stmt = $db->prepare("
        SELECT t.*, w.nama_wisata, w.lokasi, w.gambar, w.harga_tiket, ti.kode_tiket, ti.tanggal_kunjungan 
        FROM transaksi t
        JOIN wisata w ON t.id_wisata = w.id
        LEFT JOIN tiket ti ON t.id = ti.id_transaksi
        WHERE t.id_user = :id_user
        ORDER BY t.id DESC
    ");
    $stmt->execute(['id_user' => $id_user]);
    $riwayat_pemesanan = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Wisatawan - Pariwisata Karawang</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #0288D1;
            --light-bg: #F1F8E9;
            --dark-color: #1A3015;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FAFDF6;
        }

        .avatar-pembeli {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 700;
        }

        .ticket-status-badge {
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.82rem;
        }

        .card-ticket-item {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            background-color: white;
            transition: all 0.3s;
        }

        .card-ticket-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(46, 125, 50, 0.08);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-white fs-4" href="./../index.php">
                <i class="fa-solid fa-compass text-success me-2"></i>
                <span>Portal Wisatawan</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navDashboard">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navDashboard">
                <ul class="navbar-nav ms-auto gap-2">
                    <li class="nav-item"><a class="nav-link text-white-50" href="index.php">Beranda Publik</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm text-white rounded-pill px-3.5" href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Keluar Sesi</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <!-- HEADER PROFILE BIODATA -->
        <div class="card p-4 border-0 mb-5 shadow-sm rounded-4 bg-white">
            <div class="d-flex flex-column flex-sm-row align-items-center gap-4">
                <div class="avatar-pembeli">
                    <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                </div>
                <div class="text-center text-sm-start flex-grow-1">
                    <span class="badge bg-success mb-1 px-3 py-1.5 rounded-pill"><i class="fa-solid fa-user-check me-1"></i> Peran: Wisatawan</span>
                    <h2 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($_SESSION['nama']) ?></h2>
                    <p class="text-muted mb-0"><i class="fa-solid fa-envelope me-1.5"></i> <?= htmlspecialchars($_SESSION['email']) ?></p>
                </div>
                <div>
                    <a href="index.php#daftar-wisata" class="btn btn-success px-4 py-2.5 rounded-3 border-0 shadow-sm" style="background-color: var(--primary-color);">
                        <i class="fa-solid fa-plus-circle me-1.5"></i> Cari & Pesan Wisata Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- NOTIFICATION HANDLER -->
        <?php if (isset($_SESSION['sukses_order'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 py-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-1.5 fs-5"></i>
                <span><?= $_SESSION['sukses_order'] ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['sukses_order']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['sukses_pay'])): ?>
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 py-3 mb-4" role="alert">
                <i class="fa-solid fa-info-circle me-1.5 fs-5"></i>
                <span><?= $_SESSION['sukses_pay'] ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['sukses_pay']); ?>
        <?php endif; ?>

        <h3 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-history me-2 text-primary"></i> Riwayat Pemesanan Tiket Anda</h3>

        <?php if (count($riwayat_pemesanan) === 0): ?>
            <div class="card border-0 p-5 text-center shadow-sm rounded-4 bg-white">
                <i class="fa-solid fa-ticket-simple text-muted display-1 mb-3 opacity-30"></i>
                <h4 class="fw-bold text-dark">Anda Belum Memiliki Pesanan</h4>
                <p class="text-muted mb-4 max-w-sm mx-auto">Mari mulailah perjalanan tak terlupakan bersama kami di destinasi wisata terbaik Kabupaten Karawang.</p>
                <div>
                    <a href="index.php#daftar-wisata" class="btn btn-success btn-sm rounded-pill px-3.5 py-2 hover-up shadow border-0">
                        Ambil Tiket Pertama Anda <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($riwayat_pemesanan as $order): ?>
                    <div class="col-12">
                        <div class="card card-ticket-item p-4">
                            <div class="row align-items-center g-4">
                                <!-- Thumb Wisata -->
                                <div class="col-md-3">
                                    <div class="rounded-3 overflow-hidden shadow-sm" style="height: 140px;">
                                        <img src="<?= htmlspecialchars($order['gambar']) ?>" alt="" class="w-105 h-100" style="object-fit: cover;">
                                    </div>
                                </div>
                                <!-- Detail Pemesanan -->
                                <div class="col-md-5">
                                    <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($order['nama_wisata']) ?></h4>
                                    <p class="small text-danger mb-3"><i class="fa-solid fa-map-marker-alt"></i> <?= htmlspecialchars($order['lokasi']) ?></p>

                                    <div class="row">
                                        <div class="col-sm-6 small text-muted mb-2">
                                            <i class="fa-solid fa-calendar-alt text-success me-1"></i> Kunjungan: <strong class="text-dark"><?= date('d M Y', strtotime($order['tanggal_kunjungan'])) ?></strong>
                                        </div>
                                        <div class="col-sm-6 small text-muted mb-2">
                                            <i class="fa-solid fa-ticket-alt text-success me-1"></i> Jumlah: <strong class="text-dark"><?= $order['jumlah_tiket'] ?> Tiket</strong>
                                        </div>
                                        <div class="col-sm-6 small text-muted mb-0">
                                            <i class="fa-solid fa-money-check-dollar text-success me-1"></i> Total Tagihan: <strong class="text-success">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></strong>
                                        </div>
                                        <div class="col-sm-6 small text-muted mb-0">
                                            <i class="fa-solid fa-hashtag text-success me-1"></i> Kode Booking: <strong class="text-secondary"><?= $order['kode_tiket'] ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badges -->
                                <div class="col-md-2 text-md-center">
                                    <?php if ($order['status_pembayaran'] === 'lunas'): ?>
                                        <span class="badge bg-success ticket-status-badge text-uppercase"><i class="fa-solid fa-circle-check me-1"></i> Lunas</span>
                                    <?php elseif ($order['status_pembayaran'] === 'batal'): ?>
                                        <span class="badge bg-danger ticket-status-badge text-uppercase"><i class="fa-solid fa-circle-xmark me-1"></i> Batal</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark ticket-status-badge text-uppercase"><i class="fa-solid fa-clock me-1"></i> Menunggu Bayar</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Aksi Buttons -->
                                <div class="col-md-2 text-md-end p-2">
                                    <div class="d-flex flex-column gap-2">
                                        <?php if ($order['status_pembayaran'] === 'lunas'): ?>
                                            <!-- CETAK TIKET DIGITAL -->
                                            <a href="./tiket_digital.php?id_transaksi=<?= $order['id'] ?>" target="_blank" class="btn btn-outline-success btn-sm rounded-3">
                                                <i class="fa-solid fa-qrcode me-1"></i> Tiket Digital
                                            </a>
                                            <a href="./tiket_digital.php?id_transaksi=<?= $order['id'] ?>&print=true" target="_blank" class="btn btn-dark btn-sm rounded-3">
                                                <i class="fa-solid fa-print me-1"></i> Cetak Tiket
                                            </a>
                                        <?php elseif ($order['status_pembayaran'] === 'belum_bayar'): ?>
                                            <!-- SIMULASI PELUNASAN MANDIRI -->
                                            <a href="./bayar.php?id=<?= $order['id'] ?>&action=lunas" class="btn btn-success btn-sm rounded-3" onclick="return confirm('Apakah Anda ingin menyimulasikan konfirmasi pelunasan transfer bank secara instan?')">
                                                <i class="fa-solid fa-credit-card me-1"></i> Bayar Sekarang
                                            </a>
                                            <!-- PEMBATALAN PEMESANAN -->
                                            <a href="./bayar.php?id=<?= $order['id'] ?>&action=batal" class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Apakah anda yakin ingin membatalkan pesanan tiket ini?')">
                                                <i class="fa-solid fa-trash me-1"></i> Batalkan
                                            </a>
                                        <?php else: ?>
                                            <!-- BATAL STATE -->
                                            <button class="btn btn-outline-secondary btn-sm rounded-3" disabled>Telah Dibatalkan</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white-50 text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0 small">© 2026 Dinas Pariwisata Karawang. Selesai dibuat secara profesional.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>