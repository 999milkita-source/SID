<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/config.php';

$role = $_SESSION['role'] ?? '';
if ($role === '' && !empty($_SESSION['is_admin'])) {
    $role = 'admin';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

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
            <li><a href="<?= BASE_URL ?>penduduk/index.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>penduduk/ajukan_surat.php">Ajukan Surat</a></li>
            <li><a href="<?= BASE_URL ?>penduduk/status_surat.php">Status Surat</a></li>
            <li><a href="<?= BASE_URL ?>penduduk/logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='rt'): ?>
            <li><a href="<?= BASE_URL ?>rt/index.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>rt/verifikasi.php">Verifikasi Surat</a></li>
            <li><a href="<?= BASE_URL ?>rt/logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='rw'): ?>
            <li><a href="<?= BASE_URL ?>rw/index.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>rw/verifikasi.php">Verifikasi Surat</a></li>
            <li><a href="<?= BASE_URL ?>rw/logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='kades'): ?>
            <li><a href="<?= BASE_URL ?>kades/index.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>kades/laporan.php">Monitoring</a></li>
            <li><a href="<?= BASE_URL ?>kades/logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</aside>
</html>
