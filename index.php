<?php
session_start();
require_once 'config/koneksi.php';

/* ── Variabel untuk header ── */
$page_title = 'InfoWisata Karawang — Destinasi Wisata Terbaik';
$active_nav = 'home';

/* ── Ambil parameter pencarian ── */
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

/* ── Query database ── */
try {
    if (!empty($search)) {
        $stmt = $db->prepare(
            "SELECT * FROM wisata
             WHERE nama_wisata LIKE :q
                OR lokasi      LIKE :q
                OR deskripsi   LIKE :q
             ORDER BY id DESC"
        );
        $stmt->execute(['q' => "%$search%"]);
    } else {
        $stmt = $db->query("SELECT * FROM wisata ORDER BY id DESC");
    }
    $daftar_wisata = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $daftar_wisata = [];
    $db_error = "Gagal memuat data: " . $e->getMessage();
}

$total_wisata   = count($daftar_wisata);
$total_tiket    = array_sum(array_column($daftar_wisata, 'stok_tiket'));

/* ════ Output HTML ════════════════════════════════════════════ */
require_once 'includes/header.php';
?>

<!-- ══════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════ -->
<section class="hero" id="hero">
    <div class="hero-bg" role="presentation" aria-hidden="true"></div>
    <div class="hero-grain" aria-hidden="true"></div>

    <div class="hero-inner">
        <!-- Kiri: Copy utama -->
        <div class="hero-content">
            <p class="hero-eyebrow">
                <i class="fa-solid fa-map-pin"></i>
                Kabupaten Karawang, Jawa Barat
            </p>
            <h1 class="hero-title">
                Jelajahi<br>
                <em>Keindahan Alam</em><br>
                Karawang
            </h1>
            <p class="hero-subtitle">
                Temukan curug tersembunyi, pantai eksotis, cagar budaya bersejarah, dan pesona alam pegunungan
                — semuanya dalam satu platform e-ticketing resmi.
            </p>
            <div class="hero-actions">
                <a href="#wisata" class="hero-btn-primary">
                    <i class="fa-solid fa-map-location-dot"></i> Jelajahi Destinasi
                </a>
                <a href="#tentang" class="hero-btn-secondary">
                    Pelajari Lebih Lanjut
                </a>
            </div>

            <!-- Stats -->
            <div class="hero-stats">
                <div class="hero-stat-item">
                    <strong><?= $total_wisata ?>+</strong>
                    <span>Destinasi Wisata</span>
                </div>
                <div class="hero-stat-item">
                    <strong><?= number_format($total_tiket, 0, ',', '.') ?></strong>
                    <span>Tiket Tersedia</span>
                </div>
                <div class="hero-stat-item">
                    <strong>100%</strong>
                    <span>Harga Resmi</span>
                </div>
            </div>
        </div>

        <!-- Kanan: Search card -->
        <div class="hero-search-card">
            <h3>Cari Destinasi</h3>
            <p>Ketik nama tempat, lokasi, atau jenis wisata</p>
            <form action="index.php" method="GET">
                <div class="hero-search-input-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        type="text"
                        name="q"
                        class="hero-search-input"
                        placeholder="Contoh: Curug Cigentis, Pantai..."
                        value="<?= htmlspecialchars($search) ?>"
                        autocomplete="off">
                </div>
                <button type="submit" class="hero-search-submit">
                    <i class="fa-solid fa-search"></i> Cari Sekarang
                </button>
            </form>
        </div>
    </div><!-- /hero-inner -->
</section>
<!-- /hero -->


<!-- ══════════════════════════════════════════════════
     DAFTAR WISATA
══════════════════════════════════════════════════ -->
<main class="section-main" id="wisata">

    <!-- Section Header + Search -->
    <div class="section-header">
        <div>
            <span class="section-eyebrow">Destinasi Pilihan</span>
            <h2 class="section-title">
                Eksplorasi<br>
                <em>Karawang</em>
            </h2>
        </div>

        <!-- Form pencarian standalone (halaman biasa) -->
        <form action="index.php" method="GET" class="search-bar">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
                type="text"
                name="q"
                placeholder="Nama pantai, curug, lokasi candi..."
                value="<?= htmlspecialchars($search) ?>"
                autocomplete="off">
            <button type="submit">
                <i class="fa-solid fa-search"></i> Cari
            </button>
        </form>
    </div>

    <!-- Pesan error database -->
    <?php if (!empty($db_error)): ?>
        <div class="alert-error" role="alert">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($db_error) ?>
        </div>
    <?php endif; ?>

    <!-- Hasil pencarian -->
    <?php if (!empty($search)): ?>
        <p style="margin-bottom: 24px; font-size: 0.9rem; color: var(--muted);">
            <?= count($daftar_wisata) ?> hasil untuk
            "<strong style="color:var(--forest)"><?= htmlspecialchars($search) ?></strong>" &nbsp;
            <a href="index.php#wisata" style="color:var(--mint); font-weight:600; text-decoration:none;">
                <i class="fa-solid fa-times"></i> Reset
            </a>
        </p>
    <?php endif; ?>

    <!-- Empty state -->
    <?php if (empty($daftar_wisata)): ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fa-solid fa-map-pin"></i></div>
            <h3>Destinasi tidak ditemukan</h3>
            <p>
                Tidak ada wisata yang cocok dengan
                "<?= htmlspecialchars($search) ?>".
                Coba kata kunci lain.
            </p>
            <a href="index.php#wisata" class="btn-forest">
                <i class="fa-solid fa-list"></i> Lihat Semua Destinasi
            </a>
        </div>

    <?php else: ?>
        <!-- Grid wisata -->
        <div class="wisata-grid">
            <?php foreach ($daftar_wisata as $wisata): ?>
                <article class="wisata-card">
                    <div class="wisata-card-img-wrap">
                        <img
                            src="<?= htmlspecialchars($wisata['gambar']) ?>"
                            alt="Foto <?= htmlspecialchars($wisata['nama_wisata']) ?>"
                            class="wisata-card-img"
                            loading="lazy">
                        <span class="wisata-card-badge">
                            <i class="fa-solid fa-leaf"></i> Karawang
                        </span>
                        <span class="wisata-card-price">
                            Rp <?= number_format($wisata['harga_tiket'], 0, ',', '.') ?>
                            <span class="per">/orang</span>
                        </span>
                    </div>

                    <div class="wisata-card-body">
                        <p class="wisata-card-location">
                            <i class="fa-solid fa-location-dot"></i>
                            <?= htmlspecialchars($wisata['lokasi']) ?>
                        </p>
                        <h3 class="wisata-card-title">
                            <?= htmlspecialchars($wisata['nama_wisata']) ?>
                        </h3>
                        <p class="wisata-card-desc">
                            <?= htmlspecialchars($wisata['deskripsi']) ?>
                        </p>

                        <div class="wisata-card-footer">
                            <span class="wisata-card-stock">
                                Stok: <strong><?= (int)$wisata['stok_tiket'] ?> tiket</strong>
                            </span>
                            <a href="detail.php?id=<?= (int)$wisata['id'] ?>" class="wisata-card-cta">
                                Detail <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>
<!-- /section-main -->


<!-- ══════════════════════════════════════════════════
     TENTANG
══════════════════════════════════════════════════ -->
<section class="section-tentang" id="tentang">
    <div class="tentang-inner">

        <!-- Gambar -->
        <div class="tentang-img-wrap">
            <img
                src="https://images.unsplash.com/photo-1604999333679-b86d54738315?q=80&w=900"
                alt="Pemandangan alam Karawang"
                class="tentang-img"
                loading="lazy">
            <div class="tentang-img-badge">
                <strong>2026</strong>
                <span>Melayani wisatawan</span>
            </div>
        </div>

        <!-- Konten -->
        <div class="tentang-content">
            <span class="section-eyebrow">Tentang Platform</span>
            <h2 class="section-title" style="margin-bottom: 20px;">
                Gerbang Digital<br>Pariwisata <em>Karawang</em>
            </h2>
            <p>
                InfoWisata Karawang adalah portal e-ticketing dan sistem informasi pemasaran
                pariwisata yang dikelola bersama pengelola destinasi wisata lokal. Berkomitmen
                menghadirkan transformasi digital agar wisatawan bisa membeli tiket secara
                transparan, mudah, dan tanpa antrian — dari mana saja.
            </p>

            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-bolt"></i></div>
                    <p>E-Ticket Digital Instan</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <p>Pembayaran Aman & Terverifikasi</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-rotate"></i></div>
                    <p>Update Stok Realtime</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fa-solid fa-tags"></i></div>
                    <p>Harga Resmi Tanpa Markup</p>
                </div>
            </div>
        </div>

    </div>
</section>
<!-- /tentang -->

<?php require_once 'includes/footer.php'; ?>