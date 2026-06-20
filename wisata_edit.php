<?php
/**
 * FORM EDIT DATA WISATA (CRUD - UPDATE)
 * Memuat data lama dan mengizinkan penggantian gambar dan info operasional wisata.
 */
session_start();
require_once 'koneksi.php';

// Proteksi akses penjual
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penjual') {
    header("Location: login.php");
    exit;
}

$id_penjual = $_SESSION['user_id'];
$id_wisata = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

try {
    // Ambil data lama wisata
    $stmt_old = $db->prepare("SELECT * FROM wisata WHERE id = :id");
    $stmt_old->execute(['id' => $id_wisata]);
    $wisata = $stmt_old->fetch();

    if (!$wisata) {
        die("Data wisata tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error database: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_wisata = isset($_POST['nama_wisata']) ? trim($_POST['nama_wisata']) : '';
    $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
    $lokasi = isset($_POST['lokasi']) ? trim($_POST['lokasi']) : '';
    $harga_tiket = isset($_POST['harga_tiket']) ? intval($_POST['harga_tiket']) : 0;
    $stok_tiket = isset($_POST['stok_tiket']) ? intval($_POST['stok_tiket']) : 0;
    $jam_operasional = isset($_POST['jam_operasional']) ? trim($_POST['jam_operasional']) : '';
    $fasilitas = isset($_POST['fasilitas']) ? trim($_POST['fasilitas']) : '';
    
    // Default gunakan gambar lama
    $gambar_final = $wisata['gambar'];

    if (empty($nama_wisata) || empty($deskripsi) || empty($lokasi) || $harga_tiket <= 0 || $stok_tiket < 0 || empty($jam_operasional)) {
        $error = "Mohon isi semua field data utama pariwisata degan lengkap!";
    } else {
        // PROSES UPLOAD FILE BARU JIKA TERSEDIA
        if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['gambar_file']['tmp_name'];
            $file_name = $_FILES['gambar_file']['name'];
            $file_size = $_FILES['gambar_file']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $extensions_boleh = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($file_ext, $extensions_boleh)) {
                $error = "Ekstensi berkas gambar salah! Hanya diperbolehkan JPG, JPEG, PNG, WEBP.";
            } elseif ($file_size > 5 * 1024 * 1024) {
                $error = "Ukuran gambar terlalu besar! Maksimal berselisih 5MB.";
            } else {
                $upload_dir = 'assets/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $new_file_name = 'wisata_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
                $target_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $target_path)) {
                    $gambar_final = $target_path;
                } else {
                    $error = "Gagal mengunggah file gambar ke server lokal.";
                }
            }
        } elseif (!empty($_POST['gambar_url'])) {
            // Gunakan url jika melampirkan tautan URL baru
            $gambar_final = trim($_POST['gambar_url']);
        }

        // Simpan Editan
        if (empty($error)) {
            try {
                $stmt_update = $db->prepare("
                    UPDATE wisata 
                    SET nama_wisata = :nama_wisata, gambar = :gambar, deskripsi = :deskripsi, lokasi = :lokasi, 
                        harga_tiket = :harga_tiket, stok_tiket = :stok_tiket, jam_operasional = :jam_operasional, fasilitas = :fasilitas
                    WHERE id = :id
                ");
                $stmt_update->execute([
                    'nama_wisata' => $nama_wisata,
                    'gambar' => $gambar_final,
                    'deskripsi' => $deskripsi,
                    'lokasi' => $lokasi,
                    'harga_tiket' => $harga_tiket,
                    'stok_tiket' => $stok_tiket,
                    'jam_operasional' => $jam_operasional,
                    'fasilitas' => $fasilitas,
                    'id' => $id_wisata
                ]);

                $success = "Data pariwisata berhasil diperbarui!";
                echo "<script>setTimeout(function(){ window.location.href = 'dashboard_penjual.php'; }, 1500);</script>";
            } catch (PDOException $e) {
                $error = "Gagal menyimpan perubahan pariwisata: " . $e->getMessage();
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
    <title>Edit Wisata - Pariwisata Karawang</title>
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
                <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-edit text-success me-2"></i> Edit Destinasi Wisata: <?= htmlspecialchars($wisata['nama_wisata']) ?></h3>
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

            <form action="wisata_edit.php?id=<?= $id_wisata ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nama_wisata" class="form-label fw-semibold small text-muted">Nama Destinasi Wisata</label>
                    <input type="text" name="nama_wisata" id="nama_wisata" class="form-control" value="<?= htmlspecialchars($wisata['nama_wisata']) ?>" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="harga_tiket" class="form-label fw-semibold small text-muted">Harga Tiket Masuk (IDR)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_tiket" id="harga_tiket" class="form-control" value="<?= $wisata['harga_tiket'] ?>" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stok_tiket" class="form-label fw-semibold small text-muted">Stok Tiket Tersedia</label>
                        <input type="number" name="stok_tiket" id="stok_tiket" class="form-control" value="<?= $wisata['stok_tiket'] ?>" min="0" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="lokasi" class="form-label fw-semibold small text-muted">Amanat Lokasi / Alamat</label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control" value="<?= htmlspecialchars($wisata['lokasi']) ?>" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="jam_operasional" class="form-label fw-semibold small text-muted">Jam Operasional</label>
                        <input type="text" name="jam_operasional" id="jam_operasional" class="form-control" value="<?= htmlspecialchars($wisata['jam_operasional']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fasilitas" class="form-label fw-semibold small text-muted">Fasilitas (Dipisah dengan tanda koma)</label>
                        <input type="text" name="fasilitas" id="fasilitas" class="form-control" value="<?= htmlspecialchars($wisata['fasilitas']) ?>">
                    </div>
                </div>

                <!-- PREVIEW GAMBAR LAMA -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted d-block">Gambar Saat Ini</label>
                    <img src="<?= htmlspecialchars($wisata['gambar']) ?>" alt="" class="rounded shadow-sm" style="height: 120px; width: 200px; object-fit: cover;">
                </div>

                <!-- DUAL SOURCE GAMBAR -->
                <div class="p-3 bg-light rounded-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2.5 small"><i class="fa-solid fa-image text-success me-1.5"></i> Ubah Gambar Wisata (Opsional)</h6>
                    <div class="mb-3">
                        <label for="gambar_file" class="form-label text-muted text-xs">Unggah Berkas Baru (Akan menimpa gambar lama)</label>
                        <input type="file" name="gambar_file" id="gambar_file" class="form-control form-control-sm bg-white" accept="image/*">
                    </div>
                    <div class="mb-0">
                        <label for="gambar_url" class="form-label text-muted text-xs">Atau Tempel Tautan URL Baru</label>
                        <input type="url" name="gambar_url" id="gambar_url" class="form-control form-control-sm" placeholder="https://images.unsplash.com/photo-...">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="form-label fw-semibold small text-muted">Deskripsi Lengkap Wisata</label>
                    <textarea name="deskripsi" id="deskripsi" rows="6" class="form-control" required><?= htmlspecialchars($wisata['deskripsi']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold" style="background-color: var(--primary-color);">
                    <i class="fa-solid fa-save me-1.5"></i> Simpan Perubahan Data Wisata
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
