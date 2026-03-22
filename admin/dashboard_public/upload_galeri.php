<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('admin');

$id = $_POST['id'] ?? '';
$judul = trim($_POST['judul'] ?? '');
$file = $_FILES['foto'] ?? null;

// Fixed consistent path below

if ($judul === '') {
header("Location: galeri.php?error=data_kosong");
    exit;
}

// 👉 MODE EDIT TANPA UPLOAD BARU
if ($id && (!$file || $file['error'] === UPLOAD_ERR_NO_FILE)) {
    $stmt = $pdo->prepare("UPDATE galeri SET judul = ? WHERE id = ?");
    $stmt->execute([$judul, $id]);

header("Location: galeri.php?status=sukses_upload");
    exit();
}

// VALIDASI FILE
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
header("Location: galeri.php?error=upload_error");
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
header("Location: galeri.php?error=ukuran_terlalu_besar");
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allow = ['jpg','jpeg','png','webp'];

if (!in_array($ext, $allow)) {
header("Location: galeri.php?error=format_salah");
    exit;
}

// BUAT NAMA FILE
$nama = uniqid('galeri_', true) . '.' . $ext;

// UPLOAD to root uploads/
$uploadDir = __DIR__ . '/../../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
move_uploaded_file($file['tmp_name'], $uploadDir . $nama);
// HAPUS FILE LAMA (JIKA EDIT)
if ($id) {
    $stmt = $pdo->prepare("SELECT file FROM galeri WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && file_exists($uploadDir . basename($row['file']))) {
    unlink($uploadDir . basename($row['file']));
}

    $stmt = $pdo->prepare("UPDATE galeri SET judul = ?, file = ? WHERE id = ?");
    $stmt->execute([$judul, $nama, $id]);

} else {
    $stmt = $pdo->prepare("INSERT INTO galeri (judul, file) VALUES (?, ?)");
    $stmt->execute([$judul, $nama]);
}
header("Location: galeri.php?status=sukses_upload");
exit();
