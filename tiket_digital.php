<?php

/**
 * TAMPILAN TIKET DIGITAL RESMI DENGAN KODE BARCODE & STRUK BELANJA
 * Dapat langsung dicetak (print out) oleh wisatawan.
 */
session_start();
require_once 'config/koneksi.php';

// Proteksi akses login
if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$id_transaksi = isset($_GET['id_transaksi']) ? intval($_GET['id_transaksi']) : 0;
$trigger_print = isset($_GET['print']) ? true : false;

try {
    // Ambil detail tiket, wisata, pembeli dan status transaksi
    $stmt = $db->prepare("
        SELECT t.*, u.nama AS nama_pembeli, u.email AS email_pembeli, w.nama_wisata, w.lokasi, w.jam_operasional, ti.kode_tiket, ti.tanggal_kunjungan
        FROM transaksi t
        JOIN users u ON t.id_user = u.id
        JOIN wisata w ON t.id_wisata = w.id
        JOIN tiket ti ON t.id = ti.id_transaksi
        WHERE t.id = :id_transaksi
    ");
    $stmt->execute(['id_transaksi' => $id_transaksi]);
    $tiket = $stmt->fetch();

    if (!$tiket) {
        die("Data tiket digital tidak ditemukan.");
    }

    // Hanya pembeli bersangkutan atau penjual yang bisa melihat tiket
    if ($_SESSION['role'] !== 'penjual' && $tiket['id_user'] !== $_SESSION['user_id']) {
        die("Anda tidak diizinkan mengakses tiket ini.");
    }

    if ($tiket['status_pembayaran'] !== 'lunas') {
        die("E-Tiket belum aktif. Silakan lakukan pembayaran terlebih dahulu.");
    }
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticket Resmi #<?= htmlspecialchars($tiket['kode_tiket']) ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #ededed;
            color: #333;
            padding: 40px 10px;
        }

        .ticket-box {
            background-color: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            max-width: 650px;
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .ticket-header {
            background: linear-gradient(135deg, #1B5E20, #2E7D32);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 3px dashed #ffffff;
            position: relative;
        }

        .ticket-header::after,
        .ticket-header::before {
            content: '';
            position: absolute;
            bottom: -15px;
            width: 30px;
            height: 30px;
            background-color: #ededed;
            border-radius: 50%;
        }

        .ticket-header::before {
            left: -15px;
        }

        .ticket-header::after {
            right: -15px;
        }

        .ticket-body {
            padding: 40px 30px;
        }

        .qr-placeholder {
            width: 150px;
            height: 150px;
            border: 4px solid var(--bs-success);
            padding: 5px;
            border-radius: 12px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
        }

        .verified-stamp {
            border: 3px solid #2E7D32;
            color: #2E7D32;
            text-transform: uppercase;
            font-size: 1.1rem;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 8px;
            transform: rotate(-5deg);
            display: inline-block;
            letter-spacing: 1px;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }

            .ticket-box {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }

            .btn-print-action {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="text-center mb-4 btn-print-action">
        <button onclick="window.print()" class="btn btn-dark btn-lg rounded-pill px-4 shadow">
            <i class="fa-solid fa-print me-1.5"></i> Cetak E-Tiket Wisatawan
        </button>
        <a href="dashboard_pembeli.php" class="btn btn-outline-success btn-lg rounded-pill px-4 ms-2">
            Kembali ke Dashboard
        </a>
    </div>

    <div class="ticket-box animate__animated animate__zoomIn">
        <!-- Header -->
        <div class="ticket-header">
            <i class="fa-solid fa-compass fs-1 mb-2 text-warning"></i>
            <h3 class="fw-bold mb-1">E-TIKET PARIWISATA OFFICIAL</h3>
            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold text-uppercase tracking-wider">KABUPATEN KARAWANG</span>
        </div>

        <!-- Body -->
        <div class="ticket-body">
            <div class="text-center mb-5">
                <span class="text-muted text-xs d-block mb-1">KODE PINTU GERBANG (KODE TIKET)</span>
                <h1 class="fw-bold text-success font-monospace mb-2"><?= htmlspecialchars($tiket['kode_tiket']) ?></h1>
                <div class="my-3">
                    <!-- QR Code Simulator in pure visual layout -->
                    <div class="qr-placeholder shadow-sm">
                        <!-- Simulated QR Code blocks grid -->
                        <div style="background-image: url('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $tiket['kode_tiket'] ?>'); background-size: cover; width: 140px; height: 140px;"></div>
                    </div>
                </div>
                <div class="verified-stamp my-2">
                    <i class="fa-solid fa-circle-check"></i> Verified Ok
                </div>
            </div>

            <div class="row border-top pt-4 g-4">
                <div class="col-6 mb-3">
                    <span class="text-muted text-xs d-block">DESTINASI WISATA</span>
                    <strong class="text-dark fs-5"><?= htmlspecialchars($tiket['nama_wisata']) ?></strong>
                </div>
                <div class="col-6 mb-3">
                    <span class="text-muted text-xs d-block">TANGGAL KUNJUNGAN</span>
                    <strong class="text-dark fs-5"><?= date('d F Y', strtotime($tiket['tanggal_kunjungan'])) ?></strong>
                </div>

                <div class="col-6 mb-3">
                    <span class="text-muted text-xs d-block">NAMA WISATAWAN</span>
                    <strong class="text-dark"><?= htmlspecialchars($tiket['nama_pembeli']) ?></strong>
                </div>
                <div class="col-6 mb-3">
                    <span class="text-muted text-xs d-block">JAM OPERASIONAL</span>
                    <strong class="text-dark"><?= htmlspecialchars($tiket['jam_operasional']) ?></strong>
                </div>

                <div class="col-6">
                    <span class="text-muted text-xs d-block">JUMLAH TIKET</span>
                    <strong class="text-dark fs-5"><?= $tiket['jumlah_tiket'] ?> Tiket Masuk</strong>
                </div>
                <div class="col-6">
                    <span class="text-muted text-xs d-block">TOTAL BAYAR (LUNAS)</span>
                    <strong class="text-success fs-5">Rp <?= number_format($tiket['total_harga'], 0, ',', '.') ?></strong>
                </div>
            </div>

            <!-- Catatan Kunjungan -->
            <div class="p-3 bg-light rounded-3 mt-5" style="border-left: 4px solid #2E7D32;">
                <h6 class="fw-bold text-dark mb-1 small">Ketentuan Berwisata:</h6>
                <ul class="mb-0 small text-muted ps-3">
                    <li>Mohon tunjukkan E-Tiket digital atau lembar kertas cetak ini ke petugas loket pintu masuk.</li>
                    <li>Tiket berlaku sesuai dengan tanggal kunjungan yang tertera di atas.</li>
                    <li>Satu lembar kode voucher e-tiket ini berlaku untuk kapasitas masuk sejumlah <?= $tiket['jumlah_tiket'] ?> orang.</li>
                    <li>Semoga liburan Anda menyenangkan dan berkesan di Kabupaten Karawang!</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Pemicu Print out otomatis -->
    <?php if ($trigger_print): ?>
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>