<?php
// navbar.php
// Theme dinamis: background halaman dibaca dari tabel info_desa (tipe='background')
$bgStyle = "linear-gradient(135deg, #ecfeff, #f8fafc)";

try {
    if (isset($pdo)) {
        $stmt = $pdo->prepare("SELECT konten FROM info_desa WHERE tipe='background' ORDER BY urutan ASC LIMIT 1");
        $stmt->execute();
        $raw = $stmt->fetchColumn();

        if ($raw) {
            $rawTrim = trim((string)$raw);
            $cfg = json_decode($rawTrim, true);

            // Format yang disarankan (konten):
            // 1) Gradient:
            //    {"type":"gradient","color1":"#ecfeff","color2":"#f8fafc"}
            // 2) Gambar:
            //    {"type":"image","file":"nama_file.jpg"}
            if (is_array($cfg) && !empty($cfg['type'])) {
                if ($cfg['type'] === 'image' && !empty($cfg['file'])) {
                    $file = (string)$cfg['file'];
                    $url = "../uploads/" . $file; // karena navbar ini ter-include di /public/*.php
                    $bgStyle = "url('" . $url . "') center / cover no-repeat fixed";
                } elseif ($cfg['type'] === 'gradient') {
                    $c1 = !empty($cfg['color1']) ? (string)$cfg['color1'] : '#ecfeff';
                    $c2 = !empty($cfg['color2']) ? (string)$cfg['color2'] : '#f8fafc';
                    $bgStyle = "linear-gradient(135deg, {$c1}, {$c2})";
                }
            } else {
                // fallback: admin bisa isi langsung CSS background
                if (stripos($rawTrim, 'linear-gradient') !== false || stripos($rawTrim, 'url(') !== false) {
                    $bgStyle = $rawTrim;
                }
            }
        }
    }
} catch (Exception $e) {
    // jika gagal parsing DB, pakai background default
}
?>

<style>
  html, body {
    background: <?= $bgStyle ?> !important;
  }
</style>
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
