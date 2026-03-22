<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
$uploadDir = __DIR__ . '/../../uploads/';

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT file FROM galeri WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['file'])) {
        $filePath = $uploadDir . basename($row['file']);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $pdo->prepare("DELETE FROM galeri WHERE id = ?")->execute([$id]);
}

// 🔥 redirect balik ke tab galeri
header("Location: info_desa.php?status=sukses_hapus");
exit();