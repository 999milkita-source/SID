<?php
require_once '_protect.php';

$filename = basename($_GET['file']);
$storageDir = realpath(__DIR__ . '/../../storage');
$filePath = $storageDir ? realpath($storageDir . DIRECTORY_SEPARATOR . $filename) : false;

if (!$storageDir || !$filePath || strpos($filePath, $storageDir) !== 0 || !file_exists($filePath)) {
    http_response_code(404);
    exit('File tidak ditemukan');
}

$previewMode = (($_GET['mode'] ?? '') === 'preview');

// mime type real
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $filePath) ?: 'application/octet-stream';
finfo_close($finfo);

// bersihkan buffer
if (ob_get_length()) ob_end_clean();

header('Content-Type: ' . $mime);
header('Content-Disposition: ' . ($previewMode ? 'inline' : 'attachment') . '; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filePath));

if (!$previewMode) {
    header('Content-Description: File Transfer');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: public');
} else {
    header('Cache-Control: private, max-age=0, must-revalidate');
}

readfile($filePath);
exit;
