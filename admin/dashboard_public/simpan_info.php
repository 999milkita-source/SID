<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('admin');

$id     = $_POST['id'] ?? '';
$judul  = trim($_POST['judul'] ?? '');
$tipe   = trim($_POST['tipe'] ?? '');
$urutan = (int)($_POST['urutan'] ?? 0);
$konten = trim($_POST['konten'] ?? '');

// Validasi untuk background: konten boleh kosong kalau upload gambar diisi
$hasFile = isset($_FILES['background_foto']) && is_array($_FILES['background_foto']) && ($_FILES['background_foto']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;

if ($judul === '' || $tipe === '') {
    die("Data tidak lengkap.");
}

if ($tipe !== 'background' && $konten === '') {
    die("Konten tidak boleh kosong.");
}

if ($tipe === 'background' && $konten === '' && !$hasFile) {
    die("Untuk background, isi salah satu: konten atau upload gambar.");
}

try {
    // Jika upload background gambar, simpan nama file ke konten sebagai JSON
    if ($tipe === 'background' && $hasFile) {
        $file = $_FILES['background_foto'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allow = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allow, true)) {
            die("Format background tidak diizinkan.");
        }

        $nama = uniqid() . '.' . $ext;
        $destDir = __DIR__ . '/../../uploads/';
        if (!is_dir($destDir)) {
            @mkdir($destDir, 0755, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $destDir . $nama)) {
            die("Gagal menyimpan file background.");
        }

        $konten = "url('../uploads/" . $nama . "') center / cover no-repeat fixed";
    }

    if ($id) {
        // UPDATE
        $stmt = $pdo->prepare("
            UPDATE info_desa 
            SET judul = ?, tipe = ?, urutan = ?, konten = ?
            WHERE id = ?
        ");
        $stmt->execute([$judul, $tipe, $urutan, $konten, $id]);

    } else {
        // INSERT
        $stmt = $pdo->prepare("
            INSERT INTO info_desa (judul, tipe, urutan, konten)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$judul, $tipe, $urutan, $konten]);
    }

  header("Location: info_desa.php?status=sukses_simpan");
exit;

} catch (PDOException $e) {
    die("Error database: " . $e->getMessage());
}