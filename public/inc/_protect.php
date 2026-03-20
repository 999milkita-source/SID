<?php
require_once '../../config/config.php';
require_once '../../config/auth.php';

ensure_session_started();
check_login();

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    http_response_code(403);
    exit('Akses ditolak');
}

if (!isset($_GET['file'])) {
    http_response_code(404);
    exit('File tidak valid');
}

$filename = basename((string) $_GET['file']);
$role = $_SESSION['role'];

if ($filename === '') {
    http_response_code(404);
    exit('File tidak valid');
}

$params = [
    'file_upload' => $filename,
    'file_final' => $filename,
];

$sql = "
    SELECT p.id
    FROM permohonan p
    JOIN users u ON p.user_id = u.id
    WHERE (
        FIND_IN_SET(:file_upload, p.file_upload)
        OR p.file_surat_admin = :file_final
    )
";

switch ($role) {
    case 'admin':
    case 'kades':
        break;
    case 'penduduk':
        $sql .= " AND p.user_id = :uid";
        $params['uid'] = (int) $_SESSION['user_id'];
        break;
    case 'rt':
        if (!isset($_SESSION['rt'])) {
            http_response_code(403);
            exit('Akses ditolak');
        }
        $sql .= " AND u.rt = :rt";
        $params['rt'] = (int) $_SESSION['rt'];
        break;
    case 'rw':
        if (!isset($_SESSION['rw'])) {
            http_response_code(403);
            exit('Akses ditolak');
        }
        $sql .= " AND u.rw = :rw";
        $params['rw'] = (int) $_SESSION['rw'];
        break;
    default:
        http_response_code(403);
        exit('Akses ditolak');
}

$sql .= " LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

if (!$stmt->fetch()) {
    http_response_code(403);
    exit('Tidak berhak mengakses file ini');
}
