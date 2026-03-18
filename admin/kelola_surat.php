<?php
require_once '../config/config.php';
require_once '../config/auth.php';

check_login();
require_role('admin');

$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);


/* ==========================
   Hapus permohonan + file
========================== */

if (isset($_POST['delete_permohonan'])) {

    $id = (int) $_POST['permohonan_id'];

    $stmt = $pdo->prepare("SELECT file_upload, file_surat_admin FROM permohonan WHERE id=?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {

        $files = [];

        if ($data['file_upload']) {
            $files = array_merge($files, explode(',', $data['file_upload']));
        }

        if ($data['file_surat_admin']) {
            $files[] = $data['file_surat_admin'];
        }

        foreach ($files as $f) {

            $path = $storage_path . $f;

            if (file_exists($path)) {
                unlink($path);
            }

        }

    }

    $pdo->prepare("DELETE FROM permohonan WHERE id=?")->execute([$id]);

    $_SESSION['success'] = "Permohonan berhasil dihapus";

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}


/* ==========================
   Upload surat admin
========================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_surat'])) {

    $permohonan_id = (int) $_POST['permohonan_id'];

    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === UPLOAD_ERR_OK) {

        $file = $_FILES['file_surat'];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowed = ['pdf','jpg','jpeg','png'];

        if (!in_array($ext, $allowed)) {

            $_SESSION['error'] = "File harus pdf/jpg/png";

        } else {

            $newName = uniqid().'_surat.'.$ext;

            if (move_uploaded_file($file['tmp_name'], $storage_path.$newName)) {

                $stmt = $pdo->prepare("
                   UPDATE permohonan 
SET file_surat_admin = :file,
status = 'selesai'
WHERE id = :id
                ");

                $stmt->execute([
                    'file'=>$newName,
                    'id'=>$permohonan_id
                ]);

                $_SESSION['success'] = "Surat berhasil diupload";

            } else {

                $_SESSION['error'] = "Upload gagal";

            }

        }

    } else {

        $_SESSION['error'] = "File tidak ditemukan";

    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}


/* ==========================
   Ambil semua permohonan
========================== */

$stmt = $pdo->query("
    SELECT 
        p.id,
        u.nama_lengkap,
        p.jenis_surat,
        p.keterangan,
        p.status,
        p.file_upload,
        p.file_surat_admin,
        p.created_at
    FROM permohonan p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
");

$permohonan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<title>Kelola Surat - Admin</title>

<link rel="stylesheet" href="assets/css/kelola_surat.css">

<style>
table img{
max-width:80px;
max-height:80px;
display:block;
margin-bottom:5px;
}
button{
margin-top:5px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

<?php include '../public/inc/sidebar.php'; ?>

<main class="content">

<h1>Kelola Surat</h1>


<?php if($success): ?>
<script>
document.addEventListener("DOMContentLoaded",function(){
Swal.fire({
icon:'success',
title:'Berhasil',
text:'<?= $success ?>'
});
});
</script>
<?php endif; ?>


<?php if($error): ?>
<script>
document.addEventListener("DOMContentLoaded",function(){
Swal.fire({
icon:'error',
title:'Gagal',
text:'<?= $error ?>'
});
});
</script>
<?php endif; ?>


<table border="1" cellpadding="5" cellspacing="0">

<tr>
<th>ID</th>
<th>Penduduk</th>
<th>Jenis Surat</th>
<th>Keterangan</th>
<th>Status</th>
<th>File Penduduk</th>
<th>File Surat</th>
<th>Aksi</th>
</tr>


<?php foreach($permohonan_list as $p): ?>

<tr>

<td><?= $p['id'] ?></td>

<td><?= htmlspecialchars($p['nama_lengkap']) ?></td>

<td><?= htmlspecialchars($p['jenis_surat']) ?></td>

<td><?= htmlspecialchars($p['keterangan']) ?></td>

<td><?= htmlspecialchars($p['status']) ?></td>


<td>

<?php if($p['file_upload']):
$files = explode(',', $p['file_upload']); ?>

<div class="file-grid">

<?php foreach($files as $f):

$ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
$file_url = $storage_url.rawurlencode($f);

if(in_array($ext,['jpg','jpeg','png'])): ?>

<img src="<?= $file_url ?>" class="lightbox-trigger" data-file="<?= $file_url ?>">

<?php else: ?>

<a href="<?= $file_url ?>" target="_blank"><?= htmlspecialchars($f) ?></a>

<?php endif; ?>

<?php endforeach; ?>

</div>

<?php else: ?>-<?php endif; ?>

</td>


<td>

<?php if($p['file_surat_admin']): ?>

<a href="<?= $storage_url.$p['file_surat_admin'] ?>" target="_blank">
Download Surat
</a>

<?php else: ?>-<?php endif; ?>

</td>


<td>

<form method="POST" enctype="multipart/form-data" style="margin-bottom:5px;">

<input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">

<input type="file" name="file_surat" required>

<button type="submit" name="upload_surat">
Upload Surat
</button>

</form>


<form method="POST" class="formHapus">

<input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">

<button type="submit" name="delete_permohonan">
Hapus
</button>

</form>

</td>

</tr>

<?php endforeach; ?>

</table>


<div id="lightbox" class="lightbox">

<span class="close">&times;</span>

<img class="lightbox-content" id="lightbox-img">

<a id="lightbox-download" class="download-btn" href="#" download>
Download
</a>

</div>  

</main>

<script src="assets/js/kelola_surat.js"></script>

</body>
</html>