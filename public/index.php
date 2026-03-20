<?php
require_once '../config/config.php';

// Ambil konten profil dari DB
$stmt = $pdo->prepare("SELECT * FROM info_desa WHERE tipe='beranda' ORDER BY urutan ASC");
$stmt->execute();
$profil = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Desa - SID Wolokota</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<?php include 'inc/navbar.php'; ?>

<main>
    <!-- Hero Section for Index -->
    <section class="hero">
        <h1>Selamat Datang di Desa Wolokota</h1>
        <p>Sistem Informasi Desa Wolokota menyediakan pelayanan terpadu untuk warga. Ajukan surat online, lihat profil desa, galeri kegiatan, dan informasi terkini.</p>
    </section>
    
    <?php foreach($profil as $section): ?>
        <section class="card">
            <h2><?= htmlspecialchars($section['judul']) ?></h2>
            <div><?= nl2br(htmlspecialchars($section['konten'])) ?></div>
        </section>
    <?php endforeach; ?>
</main>

<script>
    feather.replace();
</script>

<script src="assets/js/navbar.js"></script>
</body>
</html>
