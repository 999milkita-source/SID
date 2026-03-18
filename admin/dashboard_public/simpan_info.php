<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../../config/auth.php';

check_login();
require_role('admin');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id     = $_POST['id'] ?? '';
$judul  = trim($_POST['judul'] ?? '');
$tipe   = trim($_POST['tipe'] ?? '');
$urutan = (int)($_POST['urutan'] ?? 0);
$konten = trim($_POST['konten'] ?? '');

if ($judul === '' || $tipe === '' || $konten === '') {
    die("Data tidak lengkap.");
}

try {

    if ($id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE info_desa 
            SET judul = ?, tipe = ?, urutan = ?, konten = ?
            WHERE id = ?
        ");
        $stmt->execute([$judul, $tipe, $urutan, $konten, $id]);

    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO info_desa (judul, tipe, urutan, konten)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$judul, $tipe, $urutan, $konten]);
    }

  header("Location: info_desa.php?status=sukses_simpan");
exit;

} catch (PDOException $e) {
    die("Error database: " . $e->getMessage());
}