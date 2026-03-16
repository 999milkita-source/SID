<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../_protect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {

    $stmt = $pdo->prepare("DELETE FROM info_desa WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: info_desa.php?status=sukses_hapus");
exit;