<?php
require_once __DIR__ . '/../../config/auth.php';
ensure_session_started();
$role = $_SESSION['role'] ?? '';
?>

<!-- Mobile header / tombol menu -->
<div class="mobile-header">
    <button id="menu-toggle">&#9776;</button>
    <h3>SID Wolokota</h3>
</div>

<aside class="sidebar" id="sidebar">
    <h3>SID Wolokota</h3>
    <ul>

        <?php if($role==='admin'): ?>
            <li><a href="/sidwolokota/admin/index.php">Dashboard</a></li>
            <li><a href="/sidwolokota/admin/kelola_user.php">Kelola User</a></li>
            <li><a href="/sidwolokota/admin/dashboard_public/info_desa.php">Konten Publik</a></li>
            <li><a href="/sidwolokota/admin/kelola_surat.php">Kelola Surat</a></li>
            <li><a href="/sidwolokota/admin/logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='penduduk'): ?>
            <li><a href="/sidwolokota/penduduk/index.php">Dashboard</a></li>
            <li><a href="/sidwolokota/penduduk/ajukan_surat.php">Ajukan Surat</a></li>
            <li><a href="/sidwolokota/penduduk/status_surat.php">Status Surat</a></li>
            <li><a href="/sidwolokota/penduduk/logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='rt'): ?>
            <li><a href="/sidwolokota/rt/index.php">Dashboard</a></li>
            <li><a href="/sidwolokota/rt/verifikasi.php">Verifikasi Surat</a></li>
            <li><a href="/sidwolokota/rt/logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='rw'): ?>
            <li><a href="/sidwolokota/rw/index.php">Dashboard</a></li>
            <li><a href="/sidwolokota/rw/verifikasi.php">Verifikasi Surat</a></li>
            <li><a href="/sidwolokota/rw/logout.php">Logout</a></li>
        <?php endif; ?>

        <?php if($role==='kades'): ?>
            <li><a href="/sidwolokota/kades/index.php">Dashboard</a></li>
            <li><a href="/sidwolokota/kades/laporan.php">Monitoring</a></li>
            <li><a href="/sidwolokota/kades/logout.php">Logout</a></li>
        <?php endif; ?>

    </ul>
</aside>