<?php

/**
 * includes/footer.php
 * Dipanggil dengan: require_once 'includes/footer.php';
 * Pastikan dipanggil setelah semua konten halaman selesai.
 */
?>

<!-- ══════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════ -->
<footer class="site-footer" id="kontak">
    <div class="footer-inner">

        <div class="footer-top">

            <!-- Brand Column -->
            <div>
                <a href="index.php" class="footer-brand-logo">
                    <div class="footer-brand-icon">
                        <i class="fa-solid fa-compass"></i>
                    </div>
                    <span class="footer-brand-name">Info<em>Wisata</em></span>
                </a>
                <p class="footer-brand-desc">
                    Portal e-ticketing & sistem informasi pariwisata resmi Kabupaten Karawang, Jawa Barat.
                    Nikmati liburan aman, nyaman, dan terencana — tanpa antrian dan tanpa markup harga.
                </p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter / X"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <!-- Navigasi Column -->
            <div>
                <p class="footer-col-title">Navigasi</p>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fa-solid fa-chevron-right"></i> Beranda</a></li>
                    <li><a href="index.php#wisata"><i class="fa-solid fa-chevron-right"></i> Destinasi Wisata</a></li>
                    <li><a href="index.php#tentang"><i class="fa-solid fa-chevron-right"></i> Tentang Kami</a></li>
                    <li><a href="login.php"><i class="fa-solid fa-chevron-right"></i> Login Pengelola</a></li>
                    <li><a href="register.php"><i class="fa-solid fa-chevron-right"></i> Daftar Akun</a></li>
                </ul>
            </div>

            <!-- Kategori Wisata Column -->
            <div>
                <p class="footer-col-title">Kategori</p>
                <ul class="footer-links">
                    <li><a href="#"><i class="fa-solid fa-tree"></i> Wisata Alam</a></li>
                    <li><a href="#"><i class="fa-solid fa-landmark"></i> Budaya & Sejarah</a></li>
                    <li><a href="#"><i class="fa-solid fa-umbrella-beach"></i> Wisata Pantai</a></li>
                    <li><a href="#"><i class="fa-solid fa-mosque"></i> Wisata Religi</a></li>
                    <li><a href="#"><i class="fa-solid fa-utensils"></i> Wisata Kuliner</a></li>
                </ul>
            </div>

            <!-- Kontak Column -->
            <div>
                <p class="footer-col-title">Kontak</p>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <span>Jl. Ahmad Yani No. 1, Karawang, Jawa Barat 41314</span>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fa-solid fa-envelope"></i></div>
                    <span>support@pariwisatakarawang.go.id</span>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fa-solid fa-phone"></i></div>
                    <span>+62 267 152 7393</span>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fa-solid fa-clock"></i></div>
                    <span>Senin-Jumat, 08.00-16.00 WIB</span>
                </div>
            </div>

        </div><!-- /footer-top -->

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> Dinas Pariwisata &amp; Kebudayaan Kabupaten Karawang. Hak cipta dilindungi.</p>
            <span class="footer-bottom-badge">Aul</span>
        </div>

    </div><!-- /footer-inner -->
</footer>
<!-- /site-footer -->

<!-- Main JS -->
<script src="assets/js/main.js"></script>
</body>

</html>