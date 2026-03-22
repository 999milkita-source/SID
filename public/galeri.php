<?php
require_once '../config/config.php';

// Ambil data galeri
$stmt = $pdo->query("SELECT * FROM galeri ORDER BY id DESC");
$galeri = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Galeri Desa - SID Wolokota</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<?php include 'inc/navbar.php'; ?>

<main>
  <div class="page-header">
    <h1>Galeri Desa Wolokota</h1>
    <p>Dokumentasi kegiatan, budaya, wisata, dan pelayanan masyarakat Desa Wolokota.</p>
  </div>

  <div class="galeri-grid">
    <?php if (empty($galeri)): ?>
      <div class="empty-state">
        <p>Belum ada foto galeri.</p>
      </div>
    <?php else: ?>
      <?php foreach($galeri as $g): ?>
        <div class="galeri-item">
          <img 
            src="../uploads/<?= htmlspecialchars($g['file']) ?>" 
            alt="<?= htmlspecialchars($g['judul']) ?>" 
            loading="lazy"
            onerror="this.onerror=null;this.outerHTML='<span style="color:#6b7280;font-style:italic;padding:20px;text-align:center;">Gambar tidak ditemukan: <?= htmlspecialchars($g['file']) ?></span>';"
          >
          <p><?= htmlspecialchars($g['judul']) ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

<script>
  feather.replace();
</script>

<script src="assets/js/navbar.js"></script>
</body>
</html>
