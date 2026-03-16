<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    
require_once '../../config/config.php';

// WAJIB LOGIN
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Akses ditolak');
}

// validasi parameter
if (!isset($_GET['file'])) {
    http_response_code(404);
    exit('File tidak valid');
}

$filename = basename($_GET['file']); // cegah ../

// cek file ada di database & milik user
$stmt = $pdo->prepare("
    SELECT p.id
    FROM permohonan p
    WHERE p.user_id = :uid
      AND FIND_IN_SET(:file, p.file_upload)
    LIMIT 1
");
$stmt->execute([
    'uid'  => $_SESSION['user_id'],
    'file' => $filename
]);

if (!$stmt->fetch()) {
    http_response_code(403);
    exit('Tidak berhak mengakses file ini');
}
