<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Hapus file dari uploads/
    $stmt = $pdo->prepare("SELECT file FROM galeri WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['file'])) {
        $file_path = __DIR__ . '/../../uploads/' . basename($row['file']);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Hapus DB
    $pdo->prepare("DELETE FROM galeri WHERE id = ?")->execute([$id]);
}

header("Location: info_desa.php?status=sukses_hapus_galeri");
exit;
?>

