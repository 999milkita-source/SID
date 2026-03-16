<?php
require_once '../config/config.php';
require_once '_protect.php';

$data = $pdo->query("SELECT * FROM galeri ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Galeri</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'inc/navbar.php'; ?>

<main>
<section>
<h2>Galeri Foto</h2>

<form method="post" action="upload_galeri.php" enctype="multipart/form-data">
  <input name="judul" placeholder="Judul foto" required>
  <input type="file" name="foto" required>
  <button>Upload</button>
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem">
<?php foreach($data as $g): ?>
  <div>
    <img src="../uploads/<?= $g['file'] ?>" style="width:100%;border-radius:12px">
    <p><?= htmlspecialchars($g['judul']) ?></p>
  </div>
<?php endforeach; ?>
</div>
</section>
</main>

</body>
</html>
