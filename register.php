<?php

/**
 * register.php — Pendaftaran Wisatawan Baru
 * CSS: assets/css/auth.css
 */
session_start();
require_once 'config/koneksi.php';

$error   = '';
$success = '';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama']                  ?? '');
    $email     = trim($_POST['email']                 ?? '');
    $password  =      $_POST['password']              ?? '';
    $konfirmasi =      $_POST['konfirmasi_password']   ?? '';

    if (empty($nama) || empty($email) || empty($password) || empty($konfirmasi)) {
        $error = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format alamat email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Kata sandi minimal 6 karakter.";
    } elseif ($password !== $konfirmasi) {
        $error = "Konfirmasi kata sandi tidak cocok.";
    } else {
        try {
            $check = $db->prepare("SELECT id FROM users WHERE email = :email");
            $check->execute(['email' => $email]);
            if ($check->fetch()) {
                $error = "Email ini sudah terdaftar. Gunakan email lain atau masuk.";
            } else {
                $hash   = password_hash($password, PASSWORD_DEFAULT);
                $insert = $db->prepare("INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, 'pembeli')");
                $insert->execute(['nama' => $nama, 'email' => $email, 'password' => $hash]);
                $success = "Akun berhasil dibuat! Mengarahkan ke halaman login...";
            }
        } catch (PDOException $e) {
            $error = "Gagal membuat akun. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar akun wisatawan di portal Pariwisata Karawang.">
    <title>Daftar Akun — InfoWisata Karawang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Auth CSS (shared login & register) -->
    <link rel="stylesheet" href="assets/css/auth.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="auth-shell">

        <!-- ══ FORM KIRI ═══════════════════════════════════════════ -->
        <main class="auth-panel">
            <div class="auth-form-wrap">

                <!-- Mobile: Kembali -->
                <a href="../index.php" class="auth-mobile-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
                </a>

                <!-- Header -->
                <p class="auth-panel-eyebrow">Bergabung Bersama Kami</p>
                <h2 class="auth-panel-title">Daftar Akun Wisatawan</h2>
                <p class="auth-panel-sub">Buat akun untuk mulai menjelajahi destinasi terbaik Karawang.</p>

                <!-- Alert -->
                <?php if (!empty($error)): ?>
                    <div class="auth-alert error" role="alert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="auth-alert success" role="alert">
                        <i class="fa-solid fa-circle-check"></i>
                        <span><?= htmlspecialchars($success) ?></span>
                    </div>
                    <script>
                        setTimeout(() => window.location.href = 'login.php', 2000);
                    </script>
                <?php endif; ?>

                <!-- Form -->
                <form action="register.php" method="POST" novalidate>

                    <div class="auth-field">
                        <label for="nama">Nama Lengkap</label>
                        <div class="auth-input-wrap">
                            <i class="fa-solid fa-user icon-left"></i>
                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                placeholder="Ahmad Sanusi"
                                value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                                autocomplete="name"
                                required>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label for="email">Alamat Email</label>
                        <div class="auth-input-wrap">
                            <i class="fa-solid fa-envelope icon-left"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="ahmad@email.com"
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
                                placeholder="Minimal 6 karakter"
                                autocomplete="new-password"
                                required>
                            <button type="button" class="btn-toggle-pw" aria-label="Tampilkan/sembunyikan kata sandi" data-target="password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <!-- Strength bar -->
                        <div class="pw-strength-bar" aria-hidden="true">
                            <div class="pw-strength-bar-fill" id="pw-strength-fill"></div>
                        </div>
                        <p class="auth-field-hint" id="pw-hint">Gunakan kombinasi huruf, angka, dan simbol.</p>
                    </div>

                    <div class="auth-field">
                        <label for="konfirmasi_password">Konfirmasi Kata Sandi</label>
                        <div class="auth-input-wrap">
                            <i class="fa-solid fa-check-circle icon-left"></i>
                            <input
                                type="password"
                                id="konfirmasi_password"
                                name="konfirmasi_password"
                                placeholder="Ulangi kata sandi"
                                autocomplete="new-password"
                                required>
                            <button type="button" class="btn-toggle-pw" aria-label="Tampilkan/sembunyikan konfirmasi" data-target="konfirmasi_password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-submit">
                        Buat Akun Wisatawan <i class="fa-solid fa-user-plus"></i>
                    </button>

                </form>

                <div class="auth-divider">sudah punya akun?</div>

                <p class="auth-footer-cta">
                    <a href="login.php">← Masuk ke Akun Anda</a>
                </p>

            </div>
        </main>
        <!-- /auth-panel -->

        <!-- ══ HERO KANAN ══════════════════════════════════════════ -->
        <aside class="auth-hero">
            <!-- Foto latar -->
            <div class="auth-hero-bg" style="background-image: url('https://images.unsplash.com/photo-1544735716-392fe2489ffa?q=80&w=1200');"></div>

            <!-- Hiasan SVG -->
            <svg class="auth-hero-svg" viewBox="0 0 560 900" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 660 C 100 620,180 700,300 665 C 420 630,480 710,560 675 L560 900 L0 900Z" fill="#0D2118" opacity="0.7" />
                <path d="M0 720 C 120 685,200 760,330 725 C 450 695,510 760,560 735 L560 900 L0 900Z" fill="#122A1E" opacity="0.6" />
                <path d="M0 780 C 130 752,220 815,350 782 C 460 754,515 815,560 790 L560 900 L0 900Z" fill="#173322" opacity="0.5" />
            </svg>

            <!-- Cahaya pojok (posisi berbeda dari login) -->
            <div class="auth-hero-glow" style="width:280px;height:280px;top:-60px;left:-60px;background:radial-gradient(circle, rgba(76,175,125,0.2) 0%, transparent 70%);"></div>
            <div class="auth-hero-glow" style="width:240px;height:240px;bottom:80px;right:-40px;background:radial-gradient(circle, rgba(200,150,62,0.15) 0%, transparent 70%);"></div>

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
                <p class="auth-hero-eyebrow">Akun Wisatawan Baru</p>
                <h1 class="auth-hero-title">Satu akun untuk semua <em>petualangan</em> Anda.</h1>
                <p class="auth-hero-desc">Pesan tiket, jelajahi destinasi pilihan, dan dapatkan rekomendasi wisata terbaik di Karawang — semuanya dari satu portal resmi.</p>
            </div>

            <!-- Perks -->
            <div class="auth-hero-bottom">
                <div class="auth-perks">
                    <div class="auth-perk">
                        <span class="auth-perk-icon"><i class="fa-solid fa-ticket"></i></span>
                        Pesan tiket destinasi secara online, kapan saja
                    </div>
                    <div class="auth-perk">
                        <span class="auth-perk-icon"><i class="fa-solid fa-map-location-dot"></i></span>
                        Akses ke 40+ destinasi wisata pilihan Karawang
                    </div>
                    <div class="auth-perk">
                        <span class="auth-perk-icon"><i class="fa-solid fa-bell"></i></span>
                        Info promo &amp; event wisata terbaru langsung di dashboard
                    </div>
                </div>
            </div>
        </aside>
        <!-- /auth-hero -->

    </div><!-- /auth-shell -->

    <script>
        // ── Toggle show/hide password ──────────────────────────────
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

        // ── Password strength meter ────────────────────────────────
        const pwInput = document.getElementById('password');
        const fill = document.getElementById('pw-strength-fill');
        const hint = document.getElementById('pw-hint');

        const levels = [{
                score: 0,
                color: 'transparent',
                width: '0%',
                text: 'Gunakan kombinasi huruf, angka, dan simbol.'
            },
            {
                score: 1,
                color: '#E53E3E',
                width: '25%',
                text: 'Terlalu lemah — tambahkan karakter.'
            },
            {
                score: 2,
                color: '#DD6B20',
                width: '50%',
                text: 'Cukup — bisa lebih kuat lagi.'
            },
            {
                score: 3,
                color: '#C8963E',
                width: '75%',
                text: 'Bagus — hampir sempurna.'
            },
            {
                score: 4,
                color: '#2E6B4E',
                width: '100%',
                text: 'Kuat — kata sandi aman.'
            },
        ];

        pwInput?.addEventListener('input', () => {
            const v = pwInput.value;
            let score = 0;
            if (v.length >= 6) score++;
            if (v.length >= 10) score++;
            if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
            if (/[0-9]/.test(v) && /[^A-Za-z0-9]/.test(v)) score++;

            const lvl = levels[score] || levels[0];
            fill.style.width = lvl.width;
            fill.style.background = lvl.color;
            if (hint) hint.textContent = lvl.text;
        });
    </script>

</body>

</html>