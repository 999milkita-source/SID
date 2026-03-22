<?php
require_once '../config/config.php';

// Ambil konten kontak
$stmt = $pdo->prepare("
    SELECT judul, konten 
    FROM info_desa 
    WHERE LOWER(tipe) = 'kontak' 
    ORDER BY urutan ASC
");
$stmt->execute();
$kontak = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kontak - SID Wolokota</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<?php include 'inc/navbar.php'; ?>

<main>
    <div class="page-header">
        <h1>Kontak Kami</h1>
    </div>

    <?php if (empty($kontak)): ?>
        <section class="card">
            <h2>Belum Ada Data</h2>
            <p>Konten kontak belum tersedia.</p>
        </section>
    <?php else: ?>
        <?php foreach($kontak as $section): ?>
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