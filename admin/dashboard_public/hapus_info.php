<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../config/auth.php';

ensure_session_started();
check_login();
require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {

    $stmt = $pdo->prepare("DELETE FROM info_desa WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: info_desa.php?status=sukses_hapus");
exit;