<?php
// navbar.php
?>
<nav class="navbar">
    <div class="logo">
        <h1>Desa Wolokota</h1>
    </div>

    <!-- tombol menu (mobile only) -->
    <button class="menu-toggle" id="menuToggle" aria-label="Menu">
        <i data-feather="menu"></i>
    </button>

    <!-- menu -->
    <div class="buttonnav" id="navMenu">
        <a href="index.php"><i data-feather="home"></i> Beranda</a>
        <a href="profil.php"><i data-feather="user"></i> Profil</a>
        <a href="tentang.php"><i data-feather="info"></i> Tentang</a>
        <a href="galeri.php"><i data-feather="image"></i> Galeri</a>
        <a href="kontak.php"><i data-feather="mail"></i> Kontak</a>
        <a href="login.php"><i data-feather="log-in"></i> Login</a>
    </div>
</nav>
