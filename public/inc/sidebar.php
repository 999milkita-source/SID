<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<!-- Mobile header / tombol menu -->
<div class="mobile-header">
    <button id="menu-toggle">&#9776;</button>
    <h3>SID Wolokota</h3>
</div>

<aside class="sidebar" id="sidebar">
    <h3>SID Wolokota</h3>
    <ul>
        <?php if($role==='admin'): ?>
         <li><a href="<?= BASE_URL ?>admin/index.php">Dashboard</a></li>
        <li><a href="<?= BASE_URL ?>admin/kelola_user.php">Kelola User</a></li>
        <li><a href="<?= BASE_URL ?>admin/dashboard_public/info_desa.php">Kelola Konten Publik</a></li>
        <li><a href="<?= BASE_URL ?>admin/kelola_surat.php">Kelola Surat</a></li>
        <li><a href="<?= BASE_URL ?>admin/logout.php" id="logout">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='penduduk'): ?>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="ajukan_surat.php">Ajukan Surat</a></li>
            <li><a href="status_surat.php">Status Surat</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='rt'): ?>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="verifikasi.php">Verifikasi Surat</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='rw'): ?>
            <li><a href="../rw/index.php">Dashboard</a></li>
            <li><a href="../rw/verifikasi_surat.php">Verifikasi Surat</a></li>
            <li><a href="../rw/logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='kades'): ?>
            <li><a href="../kades/index.php">Dashboard</a></li>
            <li><a href="../kades/laporan.php">Monitoring</a></li>
            <li><a href="../kades/logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</aside>
</html>