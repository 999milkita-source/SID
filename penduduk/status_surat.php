<?php
require_once '../config/config.php';
require_once '../config/auth.php';

ensure_session_started();
check_login();
require_role('penduduk');
$user_id = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare("
SELECT id, jenis_surat, status, file_upload, file_surat_admin, created_at 
FROM permohonan 
WHERE user_id=:uid 
ORDER BY created_at DESC
");
$stmt->execute(['uid'=>$user_id]);
$surat_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Surat - SID Wolokota</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/status_surat.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>

<main class="content">
<h2>Status Surat</h2>
<div class="card">
<table class="table">
<tr>
<th>Jenis Surat</th>
<th>Status</th>
<th>Tanggal</th>
<th>Dokumen Pendukung</th>
<th>Surat Jadi</th>
</tr>
<?php if(empty($surat_list)): ?>
<tr><td colspan="4" style="text-align:center;">Belum ada permohonan surat</td></tr>
<?php else: foreach($surat_list as $s): ?>
<tr>

<td><?= htmlspecialchars($s['jenis_surat']) ?></td>

<td>
<span class="badge <?= strtolower($s['status']) ?>">
<?= str_replace('_',' ',$s['status']) ?>
</span>
</td>

<td><?= date('d-m-Y', strtotime($s['created_at'])) ?></td>

<td>
<?php if($s['file_upload']):
$files = explode(',', $s['file_upload']); ?>

<?php foreach($files as $f): ?>
<a href="../public/inc/download.php?file=<?= rawurlencode($f) ?>" target="_blank">
Download <?= htmlspecialchars($f) ?>
</a><br>
<?php endforeach; ?>

<?php else: ?>-<?php endif; ?>
</td>

<td>

<?php if(!empty($s['file_surat_admin'])): ?>

<a class="btn-download"
href="../public/inc/download.php?file=<?= rawurlencode($s['file_surat_admin']) ?>"
target="_blank">

Download Surat

</a>

<?php else: ?>

Belum tersedia

<?php endif; ?>

</td>

</tr><?php endforeach; endif; ?>
</table>
</div>
</main>

<!-- Lightbox -->
<div id="lightbox" class="lightbox">
    <span class="close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
    <a id="lightbox-download" class="download-btn" href="#" download>Download</a>
</div>

<script src="assets/js/ajukan_surat.js"></script>
<script src="assets/js/status_surat.js"></script>
</body>
</html>
