<?php
require_once '../config/config.php';

// Ambil konten profil dari DB (pakai LOWER biar aman)
$stmt = $pdo->prepare("
    SELECT judul, konten 
    FROM info_desa 
    WHERE LOWER(tipe) = 'profil' 
    ORDER BY urutan ASC
");
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
    <div class="page-header">
        <h1>Profil Desa Wolokota</h1>
    </div>

    <?php if (empty($profil)): ?>
        <section class="card">
            <h2>Belum Ada Data</h2>
            <p>Konten profil belum tersedia. Silakan tambah melalui admin.</p>
        </section>
    <?php else: ?>
        <?php foreach($profil as $section): ?>
            <section class="card">
                <h2><?= htmlspecialchars($section['judul']) ?></h2>
                <div><?= nl2br(htmlspecialchars($section['konten'])) ?></div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<script>
    feather.replace();
</script>

<script src="assets/js/navbar.js"></script>
</body>
</html>