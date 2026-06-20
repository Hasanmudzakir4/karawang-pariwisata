/**
 * assets/js/main.js
 * InfoWisata Karawang — Interaksi UI Utama
 */

document.addEventListener("DOMContentLoaded", () => {
  const navbar = document.getElementById("site-navbar");
  const toggler = document.getElementById("navbar-toggler");
  const mobileNav = document.getElementById("navbar-mobile");

  /* ════════════════════════════════════════════════════════
       1. NAVBAR SHADOW SAAT SCROLL
    ════════════════════════════════════════════════════════ */
  if (navbar) {
    const onScroll = () =>
      navbar.classList.toggle("scrolled", window.scrollY > 10);
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }

  /* ════════════════════════════════════════════════════════
       2. HAMBURGER TOGGLE (MOBILE)
    ════════════════════════════════════════════════════════ */
  function closeMobileMenu() {
    if (!mobileNav) return;
    mobileNav.classList.remove("open");
    toggler?.setAttribute("aria-expanded", "false");
    mobileNav.setAttribute("aria-hidden", "true");
    toggler
      ?.querySelectorAll("span")
      .forEach((s) => s.removeAttribute("style"));
  }

  if (toggler && mobileNav) {
    toggler.addEventListener("click", () => {
      const isOpen = mobileNav.classList.toggle("open");
      toggler.setAttribute("aria-expanded", String(isOpen));
      mobileNav.setAttribute("aria-hidden", String(!isOpen));

      // Animasi hamburger → ×
      const spans = toggler.querySelectorAll("span");
      if (isOpen) {
        spans[0].style.cssText = "transform: translateY(7px) rotate(45deg)";
        spans[1].style.cssText = "opacity: 0; width: 0";
        spans[2].style.cssText = "transform: translateY(-7px) rotate(-45deg)";
      } else {
        spans.forEach((s) => s.removeAttribute("style"));
      }
    });

    // Tutup jika klik di luar navbar
    document.addEventListener("click", (e) => {
      if (!navbar.contains(e.target)) closeMobileMenu();
    });
  }

  /* ════════════════════════════════════════════════════════
       3. ACTIVE NAV — SCROLL SPY via IntersectionObserver
       Mendeteksi section yang sedang terlihat di viewport
       dan mengupdate class `active` pada semua nav link.
    ════════════════════════════════════════════════════════ */

  // Semua section yang ingin dipantau
  // id section harus sesuai dengan data-section pada nav link
  const SECTIONS = [
    { id: "site-navbar", key: "hero" }, // area paling atas / hero
    { id: "wisata", key: "wisata" },
    { id: "tentang", key: "tentang" },
    { id: "kontak", key: "kontak" },
  ];

  // Kumpulkan semua nav link (desktop + mobile) yang punya data-section
  const allNavLinks = document.querySelectorAll(".nav-link[data-section]");

  function setActiveNav(key) {
    allNavLinks.forEach((link) => {
      const match = link.dataset.section === key;
      link.classList.toggle("active", match);
    });
  }

  // Peta untuk menyimpan section mana yang sedang "masuk" viewport
  const visibleSections = new Map();

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        visibleSections.set(entry.target.id, entry.isIntersecting);
      });

      // Cari section pertama (urutan DOM) yang sedang terlihat
      for (const { id, key } of SECTIONS) {
        if (visibleSections.get(id)) {
          setActiveNav(key);
          return;
        }
      }
    },
    {
      // Picu saat minimal 15% section masuk viewport
      threshold: 0.15,
      // Kurangi area deteksi dari atas sebesar tinggi navbar
      rootMargin: `-${navbar ? navbar.offsetHeight : 68}px 0px 0px 0px`,
    },
  );

  // Mulai mengamati setiap section
  SECTIONS.forEach(({ id }) => {
    const el = document.getElementById(id);
    if (el) observer.observe(el);
  });

  // Fallback: saat halaman pertama kali dimuat, cek hash URL
  const initHash = window.location.hash.replace("#", "");
  if (initHash) {
    const match = SECTIONS.find((s) => s.id === initHash);
    if (match) setActiveNav(match.key);
  }

  /* ════════════════════════════════════════════════════════
       4. SMOOTH SCROLL — ANCHOR LINK INTERNAL
    ════════════════════════════════════════════════════════ */
  document.querySelectorAll('a[href*="#"]').forEach((link) => {
    link.addEventListener("click", (e) => {
      // Ambil bagian hash dari href
      const href = link.getAttribute("href");
      const hash = href.includes("#") ? "#" + href.split("#")[1] : null;
      if (!hash || hash === "#") return;

      const target = document.querySelector(hash);
      if (!target) return;

      e.preventDefault();
      const offset = navbar ? navbar.offsetHeight + 12 : 0;
      window.scrollTo({
        top: target.getBoundingClientRect().top + window.pageYOffset - offset,
        behavior: "smooth",
      });

      // Update URL hash tanpa reload
      history.pushState(null, "", hash);

      // Tutup mobile menu
      closeMobileMenu();
    });
  });

  /* ════════════════════════════════════════════════════════
       5. FALLBACK GAMBAR YANG GAGAL DIMUAT
    ════════════════════════════════════════════════════════ */
  document.querySelectorAll('img[loading="lazy"]').forEach((img) => {
    img.addEventListener("error", () => {
      img.src =
        "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=600";
      img.alt = "Gambar tidak tersedia";
    });
  });
});
