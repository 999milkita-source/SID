<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../_protect.php';

$judul = trim($_POST['judul'] ?? '');
$file = $_FILES['foto'] ?? null;

if ($judul === '' || !$file) {
    exit('Data tidak lengkap');
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allow = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($ext, $allow, true)) {
    exit('Format tidak diizinkan');
}

$nama = uniqid() . '.' . $ext;
move_uploaded_file($file['tmp_name'], __DIR__ . '/../../storage/' . $nama);

$stmt = $pdo->prepare("INSERT INTO galeri (judul,file) VALUES (?,?)");
$stmt->execute([$judul, $nama]);

header('Location: ' . BASE_URL . 'admin/dashboard_public/galeri.php');
exit;
