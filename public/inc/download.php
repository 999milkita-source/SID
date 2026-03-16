<?php
require_once '_protect.php';

$filename = basename($_GET['file']);
$filePath = $_SERVER['DOCUMENT_ROOT'] . '/sidwolokota/storage/' . $filename;

if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File tidak ditemukan');
}

// mime type real
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $filePath);
finfo_close($finfo);

// bersihkan buffer
if (ob_get_length()) ob_end_clean();

// FORCE DOWNLOAD (IDM + HP AMAN)
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

readfile($filePath);
exit;
