<?php
require_once '../config/config.php';
require_once '../config/auth.php';

check_login();
require_role('admin');

$stmt = $pdo->query("SELECT
    COUNT(*) AS total,
    SUM(status = 'selesai') AS selesai,
    SUM(status != 'selesai') AS pending
FROM permohonan");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
$total_permohonan = (int) ($stats['total'] ?? 0);
$total_selesai = (int) ($stats['selesai'] ?? 0);
$total_pending = (int) ($stats['pending'] ?? 0);
?>
<!DOCTYPE html>
<html>
<head>
   <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SID Wolokota</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>

<main class="content">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?></p>

    <div class="dashboard-cards">
        <div class="card"><strong>Total permohonan:</strong> <?= $total_permohonan ?></div>
        <div class="card"><strong>Permohonan selesai:</strong> <?= $total_selesai ?></div>
        <div class="card"><strong>Permohonan pending:</strong> <?= $total_pending ?></div>
    </div>

    <div style="margin-top:1rem;">
        <a href="kelola_surat.php">Kelola Permohonan Surat</a>
    </div>
</main>

<script src="assets/js/sidebar.js"></script>
</body>
</html>
