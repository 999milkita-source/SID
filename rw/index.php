<?php
session_start();
require_once '../config/config.php';
require_once '_protect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard RW - SID Wolokota</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>

<main>
    <h1>Selamat datang, RW!</h1>
    <p>Surat pending verifikasi RW:</p>
    <?php
    $rw_no = 1; // contoh RW
    $stmt = $pdo->prepare("SELECT p.*, u.nama_lengkap FROM permohonan p JOIN users u ON p.user_id=u.id WHERE status='diterima_rt' AND u.rw=:rw");
    $stmt->execute(['rw'=>$rw_no]);
    $surat_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <ul>
        <?php foreach($surat_list as $s): ?>
            <li><?= htmlspecialchars($s['nama_lengkap']) ?> - <?= htmlspecialchars($s['jenis_surat']) ?></li>
        <?php endforeach; ?>
    </ul>
</main>
</body>
</html>
