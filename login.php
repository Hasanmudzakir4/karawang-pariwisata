<?php

/**
 * login.php — Halaman Login Multi-role
 * CSS: assets/css/auth.css
 */
session_start();
require_once 'koneksi.php';

$error    = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

// Sudah login → lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    $dest = ($_SESSION['role'] === 'penjual') ? 'dashboard_penjual.php' : 'dashboard_pembeli.php';
    header("Location: $dest");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi.";
    } else {
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            $ok = $user && (
                password_verify($password, $user['password'])
                || $user['password'] === $password   // fallback plaintext (dev/XAMPP)
            );

            if ($ok) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];

                $dest = !empty($redirect)
                    ? $redirect
                    : ($user['role'] === 'penjual' ? 'dashboard_penjual.php' : 'dashboard_pembeli.php');
                header("Location: $dest");
                exit;
            } else {
                $error = "Email atau password salah. Silakan coba lagi.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem. Coba beberapa saat lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke portal e-ticketing Pariwisata Karawang.">
    <title>Masuk — InfoWisata Karawang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Auth CSS (shared login & register) -->
    <link rel="stylesheet" href="assets/css/auth.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="auth-shell">

        <!-- ══ HERO KIRI ══════════════════════════════════════════ -->
        <aside class="auth-hero">
            <!-- Foto latar -->
            <div class="auth-hero-bg" style="background-image: url('https://images.unsplash.com/photo-1604999333679-b86d54738315?q=80&w=1200');"></div>

            <!-- Hiasan SVG sawah -->
            <svg class="auth-hero-svg" viewBox="0 0 560 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 660 C 100 620,180 700,300 665 C 420 630,480 710,560 675 L560 900 L0 900Z" fill="#0D2118" opacity="0.7" />
                <path d="M0 720 C 120 685,200 760,330 725 C 450 695,510 760,560 735 L560 900 L0 900Z" fill="#122A1E" opacity="0.6" />
                <path d="M0 780 C 130 752,220 815,350 782 C 460 754,515 815,560 790 L560 900 L0 900Z" fill="#173322" opacity="0.5" />
            </svg>

            <!-- Cahaya pojok -->
            <div class="auth-hero-glow auth-hero-glow-1"></div>
            <div class="auth-hero-glow auth-hero-glow-2"></div>

            <!-- Brand -->
            <div class="auth-hero-top">
                <a href="../index.php" class="auth-brand">
                    <span class="auth-brand-icon"><i class="fa-solid fa-compass"></i></span>
                    <span class="auth-brand-text">InfoWisata Karawang<small>Kabupaten Karawang, Jawa Barat</small></span>
                </a>
            </div>

            <!-- Tombol kembali -->
            <a href="../index.php" class="auth-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>

            <!-- Copy utama -->
            <div class="auth-hero-body">
                <p class="auth-hero-eyebrow">Portal Wisatawan &amp; Pengelola</p>
                <h1 class="auth-hero-title">Jelajahi pesona <em>Karawang</em> mulai dari sini.</h1>
                <p class="auth-hero-desc">Masuk untuk memesan tiket, memantau destinasi unggulan, atau mengelola wisata di Lumbung Padi Jawa Barat.</p>
            </div>

            <!-- Stats -->
            <div class="auth-hero-bottom">
                <div class="auth-stats">
                    <div class="auth-stat">
                        <strong>40+</strong>
                        <span>Destinasi Wisata</span>
                    </div>
                    <div class="auth-stat">
                        <strong>12K+</strong>
                        <span>Wisatawan Terdaftar</span>
                    </div>
                    <div class="auth-stat">
                        <strong>4.8 <i class="fa-solid fa-star" style="font-size:0.7rem; color:var(--gold-lt);"></i></strong>
                        <span>Rerata Penilaian</span>
                    </div>
                </div>
            </div>
        </aside>
        <!-- /auth-hero -->

        <!-- ══ FORM KANAN ══════════════════════════════════════════ -->
        <main class="auth-panel">
            <div class="auth-form-wrap">

                <!-- Mobile: Kembali -->
                <a href="index.php" class="auth-mobile-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
                </a>

                <!-- Header -->
                <p class="auth-panel-eyebrow">Selamat Datang Kembali</p>
                <h2 class="auth-panel-title">Masuk ke Akun Anda</h2>
                <p class="auth-panel-sub">Silakan masukkan kredensial Anda untuk melanjutkan.</p>

                <!-- Error -->
                <?php if (!empty($error)): ?>
                    <div class="auth-alert error" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="login.php?redirect=<?= urlencode($redirect) ?>" method="POST" novalidate>

                    <div class="auth-field">
                        <label for="email">Alamat Email</label>
                        <div class="auth-input-wrap">
                            <i class="fa-solid fa-envelope icon-left"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="nama@email.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                autocomplete="email"
                                required>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label for="password">Kata Sandi</label>
                        <div class="auth-input-wrap">
                            <i class="fa-solid fa-lock icon-left"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required>
                            <button type="button" class="btn-toggle-pw" aria-label="Tampilkan/sembunyikan kata sandi" data-target="password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-submit">
                        Masuk Sekarang <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>

                </form>

                <div class="auth-divider">atau</div>

                <p class="auth-footer-cta">
                    Belum memiliki akun wisatawan?<br>
                    <a href="register.php">Daftar Akun Baru →</a>
                </p>

                <!-- Demo credentials -->
                <div class="auth-demo-box">
                    <p class="auth-demo-box-title"><i class="fa-solid fa-circle-info"></i> Akun Demo</p>
                    <div class="auth-demo-row">
                        <strong>Wisatawan</strong>
                        <code>user@gmail.com</code> / <code>password123</code>
                    </div>
                    <div class="auth-demo-row">
                        <strong>Pengelola</strong>
                        <code>penjual@karawang.go.id</code> / <code>password123</code>
                    </div>
                </div>

            </div>
        </main>
        <!-- /auth-panel -->

    </div><!-- /auth-shell -->

    <script>
        // Toggle show/hide password
        document.querySelectorAll('.btn-toggle-pw').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                const icon = btn.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fa-regular fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fa-regular fa-eye';
                }
            });
        });
    </script>

</body>

</html>