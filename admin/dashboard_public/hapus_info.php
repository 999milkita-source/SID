<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

ensure_session_started();
check_login();
require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Jika tipe background berupa gambar, hapus file juga (opsional tapi rapi)
    $stmt = $pdo->prepare("SELECT tipe, konten FROM info_desa WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && ($row['tipe'] ?? '') === 'background') {
        $konten = trim((string)($row['konten'] ?? ''));
        $cfg = json_decode($konten, true);
        if (is_array($cfg) && ($cfg['type'] ?? '') === 'image' && !empty($cfg['file'])) {
            $file = basename((string)$cfg['file']);
            $path = __DIR__ . '/../../uploads/' . $file;
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    $pdo->prepare("DELETE FROM info_desa WHERE id = ?")->execute([$id]);
}

header("Location: info_desa.php?status=sukses_hapus");
exit;