<?php
require_once '../config/config.php';

// ambil data galeri
$stmt = $pdo->prepare("SELECT * FROM galeri ORDER BY created_at DESC");
$stmt->execute();
$galeri = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Galeri Desa - SID Wolokota</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- feather icon -->
    <script src="https://unpkg.com/feather-icons"></script>

</head>
<body>

<?php include 'inc/navbar.php'; ?>

<main>
  <section>
    <h2>Galeri Desa Wolokota</h2>
    <p>Dokumentasi kegiatan, budaya, wisata, dan pelayanan masyarakat Desa Wolokota.</p>

    <div class="galeri-grid">
      <?php foreach($galeri as $g): ?>
        <div class="galeri-item">
          <img src="../uploads/<?= htmlspecialchars($g['file']) ?>" alt="<?= htmlspecialchars($g['judul']) ?>">
          <p><?= htmlspecialchars($g['judul']) ?></p>
        </div>
      <?php endforeach; ?>

      <?php if(count($galeri) === 0): ?>
        <p style="grid-column:1/-1;text-align:center;color:#64748b">
          Belum ada foto galeri.
        </p>
      <?php endif; ?>
    </div>
  </section>
</main>

<script>
  feather.replace();
</script>
<script src="assets/js/navbar.js"></script>
</body>
</html>
