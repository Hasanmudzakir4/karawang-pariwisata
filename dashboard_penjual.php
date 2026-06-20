<?php

session_start();
require_once 'koneksi.php';

// Proteksi akses penjual
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: login.php");
    exit;
}

$id_penjual = $_SESSION['user_id'];

try {
    // 1. Ambil rangkuman statistik penjualan
    // A. Total Wisata
    $stmt_tw = $db->query("SELECT COUNT(*) FROM wisata");
    $total_wisata = $stmt_tw->fetchColumn();

    // B. Total Tiket Terjual (Hanya yang berstatus lunas)
    $stmt_tj = $db->query("SELECT SUM(jumlah_tiket) FROM transaksi WHERE status_pembayaran = 'lunas'");
    $total_tiket_terjual = $stmt_tj->fetchColumn() ?: 0;

    // C. Total Pendapatan Kotor (Lunas)
    $stmt_pd = $db->query("SELECT SUM(total_harga) FROM transaksi WHERE status_pembayaran = 'lunas'");
    $total_pendapatan = $stmt_pd->fetchColumn() ?: 0;


    // 2. Ambil data wisata untuk kelola CRUD
    $stmt_wisata = $db->prepare("SELECT * FROM wisata ORDER BY id DESC");
    $stmt_wisata->execute();
    $daftar_wisata = $stmt_wisata->fetchAll();


    // 3. Ambil laporan seluruh transaksi masuk
    $stmt_tx = $db->query("
        SELECT t.*, u.nama AS nama_pembeli, u.email AS email_pembeli, w.nama_wisata, ti.kode_tiket, ti.tanggal_kunjungan
        FROM transaksi t
        JOIN users u ON t.id_user = u.id
        JOIN wisata w ON t.id_wisata = w.id
        LEFT JOIN tiket ti ON t.id = ti.id_transaksi
        ORDER BY t.id DESC
    ");
    $daftar_transaksi = $stmt_tx->fetchAll();
} catch (PDOException $e) {
    die("Kesalahan database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengelola - Pariwisata Karawang</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #0288D1;
            --light-bg: #E8F5E9;
            --dark-color: #1A3015;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FAFDF6;
        }

        .metric-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            background-color: white;
            transition: transform 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-2px);
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .panel-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: none;
            padding: 30px;
            margin-bottom: 40px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-success fs-4" href="index.php">
                <i class="fa-solid fa-compass me-2"></i>
                <span class="text-white">Panel Pengelola Wisata</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navDashboard">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navDashboard">
                <ul class="navbar-nav ms-auto gap-2">
                    <li class="nav-item"><a class="nav-link text-white-50" href="index.php">Beranda Pemesanan</a></li>
                    <li class="nav-item"><a class="btn btn-outline-danger btn-sm text-white rounded-pill px-3.5" href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Keluar</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <!-- HEADER ADMIN PROFILE -->
        <div class="card p-4 border-0 mb-5 shadow-sm rounded-4 bg-white">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                <div>
                    <span class="badge bg-primary mb-1 px-3 py-1.5 rounded-pill"><i class="fa-solid fa-user-shield me-1"></i> Peran: Pengelola Wisata Dinas</span>
                    <h2 class="fw-bold mb-0 text-dark">Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?></h2>
                    <p class="text-muted mb-0 small"><i class="fa-solid fa-info-circle me-1 text-success"></i> Kelola data pariwisata Karawang Anda dengan cepat secara real-time.</p>
                </div>
                <div>
                    <a href="wisata_tambah.php" class="btn btn-success px-4 py-2.5 rounded-3 border-0 shadow-sm" style="background-color: var(--primary-color);">
                        <i class="fa-solid fa-plus-circle me-1.5"></i> Daftarkan Destinasi Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- STATS SECTION -->
        <div class="row g-4 mb-5">
            <!-- Card Wisata -->
            <div class="col-md-4">
                <div class="card metric-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium d-block mb-1">Total Destinasi Wisata</span>
                        <h2 class="fw-bold text-dark mb-0"><?= $total_wisata ?></h2>
                    </div>
                    <div class="bg-light-success p-3 rounded-circle text-success" style="background-color: #E8F5E9; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fa-solid fa-map-marked-alt"></i>
                    </div>
                </div>
            </div>
            <!-- Card Tiket -->
            <div class="col-md-4">
                <div class="card metric-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium d-block mb-1">Total Tiket Terjual (Lunas)</span>
                        <h2 class="fw-bold text-primary mb-0"><?= $total_tiket_terjual ?> <span class="fs-6 text-muted font-normal">tiket</span></h2>
                    </div>
                    <div class="p-3 rounded-circle text-primary" style="background-color: #E1F5FE; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                </div>
            </div>
            <!-- Card Pendapatan -->
            <div class="col-md-4">
                <div class="card metric-card p-4 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-medium d-block mb-1">Total Pendapatan Tiket (Lunas)</span>
                        <h2 class="fw-bold text-success mb-0">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h2>
                    </div>
                    <div class="p-3 rounded-circle text-success" style="background-color: #F1F8E9; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 1: KELOLA DATA WISATA (CRUD) -->
        <section class="panel-section" id="kelola-wisata">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-layer-group text-success me-2"></i> Inventori Destinasi Wisata Anda</h4>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Wisata</th>
                            <th>Lokasi</th>
                            <th>Harga Tiket</th>
                            <th>Sisa Tiket</th>
                            <th>Jam Operasional</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($daftar_wisata) === 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada pariwisata yang didaftarkan.</td>
                            </tr>
                            <?php else: $no = 1;
                            foreach ($daftar_wisata as $wis): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($wis['gambar']) ?>" alt="" class="rounded-3" style="width: 70px; height: 45px; object-fit: cover;">
                                    </td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($wis['nama_wisata']) ?></td>
                                    <td class="small text-muted text-truncate" style="max-width: 150px;"><?= htmlspecialchars($wis['lokasi']) ?></td>
                                    <td class="fw-bold text-success">Rp <?= number_format($wis['harga_tiket'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($wis['stok_tiket'] <= 50): ?>
                                            <span class="badge bg-danger rounded-pill"><?= $wis['stok_tiket'] ?> tiket</span>
                                        <?php else: ?>
                                            <span class="badge bg-success rounded-pill"><?= $wis['stok_tiket'] ?> tiket</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($wis['jam_operasional']) ?></td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="wisata_edit.php?id=<?= $wis['id'] ?>" class="btn btn-sm btn-outline-info rounded-circle" title="Edit Wisata"><i class="fa-solid fa-edit"></i></a>
                                            <a href="wisata_hapus.php?id=<?= $wis['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Apakah Anda yakin ingin menghapus pariwisata <?= htmlspecialchars($wis['nama_wisata']) ?> ini?')" title="Hapus Wisata"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- SECTION 2: LAPORAN PENJUALAN & TRANSAKSI -->
        <section class="panel-section" id="laporan">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Laporan Aktivitas Transaksi Tiket Masuk</h4>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle shadow-xs">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Tiket</th>
                            <th>Nama Pembeli</th>
                            <th>Pariwisata</th>
                            <th>Jumlah</th>
                            <th>Total Tagihan</th>
                            <th>Kunjungan</th>
                            <th class="text-center">Status Pembayaran</th>
                            <th class="text-center">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($daftar_transaksi) === 0): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada transaksi pembelian.</td>
                            </tr>
                            <?php else: $no = 1;
                            foreach ($daftar_transaksi as $tx): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-medium font-monospace text-secondary"><?= htmlspecialchars($tx['kode_tiket'] ?: 'BELUM_GEN') ?></td>
                                    <td class="small">
                                        <strong class="d-block text-dark"><?= htmlspecialchars($tx['nama_pembeli']) ?></strong>
                                        <span class="text-muted text-xs"><?= htmlspecialchars($tx['email_pembeli']) ?></span>
                                    </td>
                                    <td class="fw-medium small"><?= htmlspecialchars($tx['nama_wisata']) ?></td>
                                    <td class="text-center fw-bold"><?= $tx['jumlah_tiket'] ?></td>
                                    <td class="fw-bold text-success">Rp <?= number_format($tx['total_harga'], 0, ',', '.') ?></td>
                                    <td class="small"><?= date('d-m-Y', strtotime($tx['tanggal_kunjungan'])) ?></td>
                                    <td class="text-center">
                                        <?php if ($tx['status_pembayaran'] === 'lunas'): ?>
                                            <span class="badge bg-success rounded-pill px-2.5 py-1 text-uppercase">Lunas</span>
                                        <?php elseif ($tx['status_pembayaran'] === 'batal'): ?>
                                            <span class="badge bg-danger rounded-pill px-2.5 py-1 text-uppercase">Batal</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 text-uppercase">Belum Bayar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1.5 justify-content-center">
                                            <?php if ($tx['status_pembayaran'] === 'belum_bayar'): ?>
                                                <a href="bayar.php?id=<?= $tx['id'] ?>&action=lunas" class="btn btn-xs btn-success py-1 px-2.5 rounded-3 fw-medium" onclick="return confirm('Konfirmasi pembayaran manual lunas?')">
                                                    <i class="fa-solid fa-check me-1"></i> Selesai
                                                </a>
                                                <a href="bayar.php?id=<?= $tx['id'] ?>&action=batal" class="btn btn-xs btn-outline-danger py-1 px-2.5 rounded-3 fw-medium" onclick="return confirm('Batalkan transaksi ini secara permanen?')">
                                                    <i class="fa-solid fa-times me-1"></i> Batal
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-xs btn-outline-secondary py-1 px-2.5 rounded-3" disabled>Verifikasi Selesai</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white-50 text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0 small">© 2026 Dinas Pariwisata Karawang. Selesai dibuat secara profesional.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>