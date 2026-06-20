<?php

/**
 * includes/header.php
 * Dipanggil dengan: require_once 'includes/header.php';
 * Variabel opsional yang bisa diset sebelum include:
 *   $page_title  — judul halaman (string)
 *   $active_nav  — id menu aktif: 'home' | 'wisata' | 'tentang' | 'kontak'
 */
$page_title  = $page_title  ?? 'Sistem Informasi Pariwisata Karawang';
$active_nav  = $active_nav  ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal e-ticketing & informasi destinasi wisata resmi di Kabupaten Karawang, Jawa Barat.">
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- CSS Utama -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- ══════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════ -->
    <header class="site-navbar" id="site-navbar">
        <div class="navbar-inner">

            <!-- Logo -->
            <a href="index.php" class="navbar-logo">
                <div class="navbar-logo-icon">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <span class="navbar-logo-text">Info<em>Wisata</em> Karawang</span>
            </a>

            <!-- Nav Links Desktop -->
            <nav>
                <ul class="navbar-nav" id="navbar-links">
                    <li><a href="index.php" data-section="hero" class="nav-link <?= $active_nav === 'home'    ? 'active' : '' ?>">Home</a></li>
                    <li><a href="index.php#wisata" data-section="wisata" class="nav-link <?= $active_nav === 'wisata'  ? 'active' : '' ?>">Destinasi</a></li>
                    <li><a href="index.php#tentang" data-section="tentang" class="nav-link <?= $active_nav === 'tentang' ? 'active' : '' ?>">Tentang</a></li>
                    <li><a href="index.php#kontak" data-section="kontak" class="nav-link <?= $active_nav === 'kontak'  ? 'active' : '' ?>">Kontak</a></li>
                </ul>
            </nav>

            <!-- Action Buttons Desktop -->
            <div class="navbar-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'penjual'): ?>
                        <a href="dashboard_penjual.php" class="btn-forest">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    <?php else: ?>
                        <a href="dashboard_pembeli.php" class="btn-outline-forest">
                            <i class="fa-solid fa-ticket"></i> Tiket Saya
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-ghost">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-outline-forest">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
                    </a>
                    <a href="register.php" class="btn-forest">
                        <i class="fa-solid fa-user-plus"></i> Daftar
                    </a>
                <?php endif; ?>
            </div>

            <!-- Hamburger Mobile -->
            <button class="navbar-toggler" id="navbar-toggler" aria-label="Buka menu navigasi" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div><!-- /navbar-inner -->

        <!-- Mobile Menu -->
        <nav class="navbar-mobile" id="navbar-mobile" aria-hidden="true">
            <a href="index.php" data-section="hero" class="nav-link <?= $active_nav === 'home'    ? 'active' : '' ?>">Home</a>
            <a href="index.php#wisata" data-section="wisata" class="nav-link <?= $active_nav === 'wisata'  ? 'active' : '' ?>">Destinasi</a>
            <a href="index.php#tentang" data-section="tentang" class="nav-link <?= $active_nav === 'tentang' ? 'active' : '' ?>">Tentang</a>
            <a href="index.php#kontak" data-section="kontak" class="nav-link <?= $active_nav === 'kontak'  ? 'active' : '' ?>">Kontak</a>

            <div class="navbar-mobile-divider"></div>

            <div class="navbar-mobile-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'penjual'): ?>
                        <a href="dashboard_penjual.php" class="btn-forest">
                            <i class="fa-solid fa-chart-line"></i> Dashboard
                        </a>
                    <?php else: ?>
                        <a href="dashboard_pembeli.php" class="btn-outline-forest">
                            <i class="fa-solid fa-ticket"></i> Tiket Saya
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-ghost">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-outline-forest">Masuk</a>
                    <a href="register.php" class="btn-forest">Daftar Sekarang</a>
                <?php endif; ?>
            </div>
        </nav>

    </header>
    <!-- /site-navbar -->