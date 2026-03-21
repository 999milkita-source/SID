<?php
require_once '../config/config.php';

// Ambil konten profil dari DB
$stmt = $pdo->prepare("SELECT * FROM info_desa WHERE tipe='tentang' ORDER BY urutan ASC");
$stmt->execute();
$profil = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang Desa Wolokota - SID Wolokota</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<?php include 'inc/navbar.php'; ?>

<main>
    <div class="page-header">
        <h1>Tentang Desa Wolokota</h1>
    </div>
    
    <?php foreach($profil as $section): ?>
        <section class="content-section">
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

