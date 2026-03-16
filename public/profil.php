<?php
require_once '../config/config.php';

// Ambil konten profil dari DB
$stmt = $pdo->prepare("SELECT * FROM info_desa WHERE tipe='profil' ORDER BY urutan ASC");
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
    <?php foreach($profil as $section): ?>
        <section class="card">
            <h2><?= htmlspecialchars($section['judul']) ?></h2>
            <p><?= nl2br(htmlspecialchars($section['konten'])) ?></p>
        </section>
    <?php endforeach; ?>
</main>

<script>
    feather.replace();
</script>

<script src="assets/js/navbar.js"></script>
</body>
</html>
