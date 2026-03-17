<?php
session_start();
require_once '../config/config.php';
require_once '_protect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT jenis_surat, status, file_upload
    FROM permohonan
    WHERE user_id = :uid
    ORDER BY created_at DESC
");
$stmt->execute(['uid' => $user_id]);
$surat_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Penduduk - SID Wolokota</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
<h1>Selamat datang, <span><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Penduduk') ?></span></h1>
<p class="subtitle">Status permohonan surat Anda</p>

<div class="card">
<table class="table">
<tr>
    <th>Jenis Surat</th>
    <th>Status</th>
    <th>File Utama</th>
</tr>
<?php if(empty($surat_list)): ?>
<tr><td colspan="3" style="text-align:center;">Belum ada permohonan surat</td></tr>
<?php else: foreach($surat_list as $s): ?>
<tr>
<td><?= htmlspecialchars($s['jenis_surat']) ?></td>
<td>
    <?php 
        $statusClass = strtolower($s['status']);
        $statusText = str_replace('_',' ',$s['status']);
    ?>
    <span class="badge <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($statusText) ?></span>
</td>
<td>
<?php if(!empty($s['file_upload'])): 
    $files = explode(',', $s['file_upload']); 
    $firstFile = $files[0];
?>
<a class="btn-download" href="../storage/<?= rawurlencode($firstFile) ?>" target="_blank">Lihat File</a>
<?php else: ?>-<?php endif; ?>
</td>
</tr>
<?php endforeach; endif; ?>
</table>
</div>
</main>
<script src="assets/js/ajukan_surat.js"></script>
</body>
</html>
