<?php
require_once '../config/config.php';
require_once '../config/auth.php';

check_login();
require_role('rt');

$user_rt = (int) ($_SESSION['rt'] ?? 0);

$stmt = $pdo->prepare("SELECT p.id, p.jenis_surat, p.status, p.created_at, u.nama_lengkap FROM permohonan p JOIN users u ON p.user_id=u.id WHERE u.rt=:rt AND p.status='menunggu_rt' ORDER BY p.created_at DESC");
$stmt->execute(['rt' => $user_rt]);
$surat_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard RT - SID Wolokota</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
<h1>Selamat datang, <span><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'RT') ?></span></h1>
<p class="subtitle">Permohonan yang menunggu verifikasi RT</p>

<div class="card">
<table class="table">
<tr>
    <th>Nama</th>
    <th>Jenis Surat</th>
    <th>Status</th>
    <th>Tanggal</th>
</tr>
<?php if(empty($surat_list)): ?>
<tr><td colspan="3" style="text-align:center;">Belum ada permohonan surat</td></tr>
<?php else: foreach($surat_list as $s): ?>
<tr>
<td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
<td><?= htmlspecialchars($s['jenis_surat']) ?></td>
<td><span class="badge <?= htmlspecialchars(strtolower($s['status'])) ?>"><?= htmlspecialchars(str_replace('_',' ',$s['status'])) ?></span></td>
<td><?= date('d-m-Y H:i', strtotime($s['created_at'])) ?></td>
</tr>
<?php endforeach; endif; ?>
</table>
</div>
</main>
<script src="assets/js/ajukan_surat.js"></script>
</body>
</html>
