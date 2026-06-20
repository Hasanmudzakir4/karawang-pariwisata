<?php
session_start();
require_once 'koneksi.php';

// Memvalidasi ID Wisata
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    $stmt = $db->prepare("SELECT * FROM wisata WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $wisata = $stmt->fetch();

    if (!$wisata) {
        die("Destinasi wisata tidak ditemukan atau telah dihapus.");
    }
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail: <?= htmlspecialchars($wisata['nama_wisata']) ?> - Karawang</title>
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

        .btn-primary-custom {
            background-color: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background-color: #1B5E20;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
        }

        .btn-secondary-custom {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-secondary-custom:hover {
            background-color: #01579B;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
        }

        .gallery-img {
            width: 100%;
            height: 480px;
            object-fit: cover;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .facility-badge {
            background-color: var(--light-bg);
            color: var(--primary-color);
            border: 1px solid rgba(46, 125, 50, 0.15);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.88rem;
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 8px;
        }

        .sticky-card {
            position: sticky;
            top: 100px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-success" href="index.php">
                <i class="fa-solid fa-compass me-2 fs-3"></i>
                <span class="fs-4">InfoWisata</span><span class="fs-4 text-primary">Karawang</span>
            </a>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row g-5">
            <!-- INFO MAIN COLUMN -->
            <div class="col-lg-8">
                <!-- Cover Image -->
                <img src="<?= htmlspecialchars($wisata['gambar']) ?>" alt="<?= htmlspecialchars($wisata['nama_wisata']) ?>" class="gallery-img mb-4 img-fluid">

                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success px-3 py-2 fs-7 rounded-pill"><i class="fa-solid fa-ticket"></i> E-Ticket Resmi</span>
                    <span class="badge bg-info px-3 py-2 fs-7 text-dark rounded-pill"><i class="fa-solid fa-clock"></i> <?= htmlspecialchars($wisata['jam_operasional']) ?></span>
                </div>

                <h1 class="fw-bold text-dark display-5 mb-3"><?= htmlspecialchars($wisata['nama_wisata']) ?></h1>

                <p class="text-danger fw-semibold mb-4 fs-5 d-flex align-items-center gap-1">
                    <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($wisata['lokasi']) ?>
                </p>

                <h4 class="fw-bold text-dark mb-3">Deskripsi Lengkap</h4>
                <div class="text-muted leading-relaxed mb-5" style="font-size: 1.05rem;">
                    <?= nl2br(htmlspecialchars($wisata['deskripsi'])) ?>
                </div>

                <h4 class="fw-bold text-dark mb-3">Fasilitas Tersedia</h4>
                <div class="mb-5">
                    <?php
                    if (!empty($wisata['fasilitas'])) {
                        $facilities = explode(',', $wisata['fasilitas']);
                        foreach ($facilities as $facility) {
                            echo '<span class="facility-badge"><i class="fa-solid fa-check text-success me-1"></i> ' . htmlspecialchars(trim($facility)) . '</span>';
                        }
                    } else {
                        echo '<p class="text-muted">Fasilitas utama, tempat parkir, toilet, dan gazebo standar.</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- BOOKING COLUMN STICKY -->
            <div class="col-lg-4">
                <div class="card sticky-card p-4 bg-white border-0">
                    <h4 class="fw-bold text-dark mb-3 text-center">Pemesanan Tiket</h4>
                    <hr class="mb-4">

                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Harga per Tiket</span>
                        <h4 class="fw-bold text-success mb-0">Rp <?= number_format($wisata['harga_tiket'], 0, ',', '.') ?></h4>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Ketersediaan Stok</span>
                        <span class="badge bg-success rounded-pill px-2.5 py-1"><?= $wisata['stok_tiket'] ?> tiket</span>
                    </div>

                    <!-- LOGIKA AUTENTIKASI DI TOMBOL PESAN TIKET -->
                    <?php if (!$is_logged_in): ?>
                        <div class="alert alert-warning border-0 shadow-sm rounded-3 py-3" role="alert">
                            <i class="fa-solid fa-lock text-warning me-1.5 fs-5"></i>
                            <span class="small">Silakan <strong>login terlebih dahulu</strong> untuk membeli tiket destinasi di Karawang.</span>
                        </div>
                        <a href="login.php?redirect=detail.php?id=<?= $wisata['id'] ?>" class="btn btn-primary-custom w-105 py-2.5 rounded-3 text-center d-block">
                            <i class="fa-solid fa-sign-in-alt me-1.5"></i> Login Sekarang
                        </a>
                    <?php else: ?>
                        <!-- FORM PEMESANAN AKTIF -->
                        <form action="pesan.php" method="POST">
                            <input type="hidden" name="id_wisata" value="<?= $wisata['id'] ?>">

                            <div class="mb-3">
                                <label for="tanggal_kunjungan" class="form-label text-muted fw-medium small">Tanggal Kunjungan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-calendar-alt text-success"></i></span>
                                    <input type="date" name="tanggal_kunjungan" id="tanggal_kunjungan" class="form-control border-start-0" required min="<?= date('Y-m-d') ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="jumlah_tiket" class="form-label text-muted fw-medium small">Jumlah Tiket</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-clipboard-user text-success"></i></span>
                                    <input type="number" name="jumlah_tiket" id="jumlah_tiket" class="form-control border-start-0" min="1" max="<?= $wisata['stok_tiket'] ?>" value="1" required>
                                </div>
                                <span class="text-muted d-block mt-1" style="font-size: 0.78rem;">Maksimal pembelian hari ini: <?= $wisata['stok_tiket'] ?> tiket</span>
                            </div>

                            <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-3 shadow-sm border-0">
                                <i class="fa-solid fa-shopping-cart me-1.5"></i> Pesan Tiket Saya
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-dark text-white-50 text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0 small">© 2026 Dinas Pariwisata Karawang. Selesai dibuat secara profesional.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>