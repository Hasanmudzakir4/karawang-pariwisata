<?php

/**
 * FORM INPUT TAMBAH DATA WISATA (CRUD - CREATE)
 * Mendukung Upload File Gambar lokal ke folder assets/uploads/ & Fallback URL Gambar.
 */
session_start();
require_once 'config/koneksi.php';

// Proteksi akses penjual
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: login.php");
    exit;
}

$id_penjual = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_wisata = isset($_POST['nama_wisata']) ? trim($_POST['nama_wisata']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
    $lokasi = isset($_POST['lokasi']) ? trim($_POST['lokasi']) : '';
    $harga_tiket = isset($_POST['harga_tiket']) ? intval($_POST['harga_tiket']) : 0;
    $stok_tiket = isset($_POST['stok_tiket']) ? intval($_POST['stok_tiket']) : 0;
    $jam_operasional = isset($_POST['jam_operasional']) ? trim($_POST['jam_operasional']) : '';
    $fasilitas = isset($_POST['fasilitas']) ? trim($_POST['fasilitas']) : '';

    $gambar_final = "https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800"; // fallback default

    if (empty($nama_wisata) || empty($deskripsi) || empty($lokasi) || $harga_tiket <= 0 || $stok_tiket < 0 || empty($jam_operasional)) {
        $error = "Mohon isi semua field data utama pariwisata degan lengkap!";
    } else {
        // PROSES UPLOAD GAMBAR LOKAL
        if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['gambar_file']['tmp_name'];
            $file_name = $_FILES['gambar_file']['name'];
            $file_size = $_FILES['gambar_file']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $extensions_boleh = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($file_ext, $extensions_boleh)) {
                $error = "Ekstensi berkas gambar salah! Hanya diperbolehkan JPG, JPEG, PNG, WEBP.";
            } elseif ($file_size > 5 * 1024 * 1024) { // Max 5MB
                $error = "Ukuran gambar terlalu besar! Maksimal berselisih 5MB.";
            } else {
                // Tentukan folder penyimpanan
                $upload_dir = 'assets/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Berikan nama acak aman biar tidak overlap
                $new_file_name = 'wisata_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                $target_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $target_path)) {
                    $gambar_final = $target_path;
                } else {
                    $error = "Gagal mengunggah file gambar ke server lokal.";
                }
            }
        } elseif (!empty($_POST['gambar_url'])) {
            // Gunakan url jika tidak mengupload file
            $gambar_final = trim($_POST['gambar_url']);
        }

        // Jalankan Database Insert jika tidak terkena error pas upload
        if (empty($error)) {
            try {
                $stmt = $db->prepare("
                    INSERT INTO wisata (id_penjual, nama_wisata, gambar, deskripsi, lokasi, harga_tiket, stok_tiket, jam_operasional, fasilitas) 
                    VALUES (:id_penjual, :nama_wisata, :gambar, :deskripsi, :lokasi, :harga_tiket, :stok_tiket, :jam_operasional, :fasilitas)
                ");
                $stmt->execute([
                    'id_penjual' => $id_penjual,
                    'nama_wisata' => $nama_wisata,
                    'gambar' => $gambar_final,
                    'deskripsi' => $deskripsi,
                    'lokasi' => $lokasi,
                    'harga_tiket' => $harga_tiket,
                    'stok_tiket' => $stok_tiket,
                    'jam_operasional' => $jam_operasional,
                    'fasilitas' => $fasilitas
                ]);

                $success = "Pariwisata baru berhasil ditambahkan!";
                echo "<script>setTimeout(function(){ window.location.href = 'dashboard_penjual.php'; }, 1500);</script>";
            } catch (PDOException $e) {
                $error = "Gagal menyimpan data pariwisata: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wisata - Pariwisata Karawang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E7D32;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FAFDF6;
        }
    </style>
</head>

<body class="py-5">

    <div class="container" style="max-width: 800px;">
        <div class="card p-4 shadow-sm border-0 rounded-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-plus-circle text-success me-2"></i> Tambah Destinasi Wisata Karawang</h3>
                <a href="dashboard_penjual.php" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-3 small" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1.5"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success border-0 rounded-3 small" role="alert">
                    <i class="fa-solid fa-circle-check me-1.5"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <form action="wisata_tambah.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nama_wisata" class="form-label fw-semibold small text-muted">Nama Destinasi Wisata</label>
                    <input type="text" name="nama_wisata" id="nama_wisata" class="form-control" placeholder="Contoh: Pantai Samudra Baru, Karawang" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="harga_tiket" class="form-label fw-semibold small text-muted">Harga Tiket Masuk (IDR)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_tiket" id="harga_tiket" class="form-control" placeholder="10000" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stok_tiket" class="form-label fw-semibold small text-muted">Stok Tiket Tersedia</label>
                        <input type="number" name="stok_tiket" id="stok_tiket" class="form-control" placeholder="150" min="0" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="lokasi" class="form-label fw-semibold small text-muted">Amanat Lokasi / Alamat</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control" placeholder="Jl. Raya Pantai No. 12, PakisJaya, Karawang" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="jam_operasional" class="form-label fw-semibold small text-muted">Jam Operasional</label>
                        <input type="text" name="jam_operasional" id="jam_operasional" class="form-control" placeholder="07:00 - 17:00 WIB" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fasilitas" class="form-label fw-semibold small text-muted">Fasilitas (Dipisah dengan tanda koma)</label>
                        <input type="text" name="fasilitas" id="fasilitas" class="form-control" placeholder="Parkiran Luas, Toilet, Musholla, Gazebo Pantai">
                    </div>
                </div>

                <!-- DUAL SOURCE GAMBAR -->
                <div class="p-3 bg-light rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2.5 small"><i class="fa-solid fa-image text-success me-1.5"></i> Masukan Gambar Wisata (Pilih Salah Satu)</h6>
                    <div class="mb-3">
                        <label for="gambar_file" class="form-label text-muted text-xs">Unggah File (Upload dari Komputer Anda)</label>
                        <input type="file" name="gambar_file" id="gambar_file" class="form-control form-control-sm bg-white" accept="image/*">
                    </div>
                    <div class="mb-0">
                        <label for="gambar_url" class="form-label text-muted text-xs">Atau Tempel Tautan URL Gambar Online</label>
                        <input type="url" name="gambar_url" id="gambar_url" class="form-control form-control-sm" placeholder="https://images.unsplash.com/photo-...">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="form-label fw-semibold small text-muted">Deskripsi Lengkap Wisata</label>
                    <textarea name="deskripsi" id="deskripsi" rows="6" class="form-control" placeholder="Ceritakan keindahan dan keunikan destinasi ini..." required></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold" style="background-color: var(--primary-color);">
                    <i class="fa-solid fa-check-circle me-1.5"></i> Simpan Destinasi Wisata
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>