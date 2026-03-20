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
   Ambil permohonan (Proses & Arsip)
========================== */

// Proses: semua yang belum selesai
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
    WHERE p.status <> 'selesai'
    ORDER BY p.created_at DESC
");
$permohonan_proses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Arsip: yang sudah selesai
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
    WHERE p.status = 'selesai'
    ORDER BY p.created_at DESC
");
$permohonan_arsip = $stmt->fetchAll(PDO::FETCH_ASSOC);

function build_file_url(string $filename, bool $preview = false): string {
    $params = ['file' => $filename];
    if ($preview) {
        $params['mode'] = 'preview';
    }

    return '../public/inc/download.php?' . http_build_query($params);
}

function get_file_extension(string $filename): string {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function is_image_file(string $filename): bool {
    return in_array(get_file_extension($filename), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
}

function render_preview_tile(string $filename): string {
    $previewUrl = build_file_url($filename, true);
    $downloadUrl = build_file_url($filename);
    $isImage = is_image_file($filename);
    $extension = strtoupper(get_file_extension($filename) ?: 'FILE');
    $metaLabel = $isImage ? 'Gambar siap dilihat' : 'Dokumen ' . $extension;

    ob_start();
    ?>
    <button
        type="button"
        class="file-preview-card preview-trigger <?= $isImage ? 'is-image' : 'is-document' ?>"
        data-preview-url="<?= htmlspecialchars($previewUrl) ?>"
        data-download-url="<?= htmlspecialchars($downloadUrl) ?>"
        data-filename="<?= htmlspecialchars($filename) ?>"
        data-filetype="<?= htmlspecialchars(get_file_extension($filename)) ?>"
    >
        <span class="preview-card-media">
            <?php if ($isImage): ?>
            <img src="<?= htmlspecialchars($previewUrl) ?>" alt="<?= htmlspecialchars($filename) ?>" loading="lazy">
            <?php else: ?>
            <span class="document-chip"><?= htmlspecialchars($extension) ?></span>
            <span class="document-hint">Klik untuk melihat dokumen</span>
            <?php endif; ?>
            <span class="preview-card-tag">
                <i data-feather="eye"></i>
                Lihat File
            </span>
        </span>
        <span class="preview-card-body">
            <span class="preview-card-name"><?= htmlspecialchars($filename) ?></span>
            <span class="preview-card-meta"><?= htmlspecialchars($metaLabel) ?></span>
        </span>
    </button>
    <?php

    return trim(ob_get_clean());
}

?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<title>Kelola Surat - Admin</title>

<link rel="stylesheet" href="assets/css/kelola_surat.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/feather-icons"></script>
<style>
    .tabs { display:flex; gap:.5rem; margin:.75rem 0 1rem; }
    .tab-link { border:0; padding:.5rem .9rem; cursor:pointer; border-radius:.5rem; background:#e5e7eb; color:#111827; }
    .tab-link.active { background:#2563eb; color:#fff; }
</style>
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

<div class="tabs">
    <button type="button" class="tab-link active" data-target="tab-proses">Proses</button>
    <button type="button" class="tab-link" data-target="tab-arsip">Arsip</button>
</div>

<div id="tab-proses" class="tab-content" style="display:block;">
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

<?php foreach($permohonan_proses as $p): ?>
<tr>
<td><?= $p['id'] ?></td>
<td><?= htmlspecialchars($p['nama_lengkap']) ?></td>
<td><?= htmlspecialchars($p['jenis_surat']) ?></td>
<td><?= htmlspecialchars($p['keterangan']) ?></td>
<td><?= htmlspecialchars($p['status']) ?></td>
<td>
<?php if($p['file_upload']):
$files = array_filter(array_map('trim', explode(',', $p['file_upload']))); ?>
<div class="file-preview-grid">
<?php foreach($files as $f): ?>
<?= render_preview_tile($f) ?>
<?php endforeach; ?>
</div>
<?php else: ?>-<?php endif; ?>
</td>
<td>
<?php if($p['file_surat_admin']): ?>
<div class="file-preview-grid">
<?= render_preview_tile($p['file_surat_admin']) ?>
</div>
<?php else: ?>-<?php endif; ?>
</td>
<td>
<form method="POST" enctype="multipart/form-data" style="margin-bottom:5px;">
<input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
<input type="file" name="file_surat" class="upload-input" accept=".pdf,.jpg,.jpeg,.png" required>
<div class="upload-preview-area is-empty">
<span class="upload-preview-placeholder">Preview file upload akan tampil di sini</span>
</div>
<button type="submit" name="upload_surat" class="file-btn primary">
Upload Surat
</button>
</form>
<form method="POST" class="formHapus">
<input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
<button type="submit" name="delete_permohonan" class="file-btn danger">
Hapus
</button>
</form>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>

<div id="tab-arsip" class="tab-content" style="display:none;">
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

<?php foreach($permohonan_arsip as $p): ?>
<tr>
<td><?= $p['id'] ?></td>
<td><?= htmlspecialchars($p['nama_lengkap']) ?></td>
<td><?= htmlspecialchars($p['jenis_surat']) ?></td>
<td><?= htmlspecialchars($p['keterangan']) ?></td>
<td><?= htmlspecialchars($p['status']) ?></td>
<td>
<?php if($p['file_upload']):
$files = array_filter(array_map('trim', explode(',', $p['file_upload']))); ?>
<div class="file-preview-grid">
<?php foreach($files as $f): ?>
<?= render_preview_tile($f) ?>
<?php endforeach; ?>
</div>
<?php else: ?>-<?php endif; ?>
</td>
<td>
<?php if($p['file_surat_admin']): ?>
<div class="file-preview-grid">
<?= render_preview_tile($p['file_surat_admin']) ?>
</div>
<?php else: ?>-<?php endif; ?>
</td>
<td>
<form method="POST" class="formHapus">
<input type="hidden" name="permohonan_id" value="<?= $p['id'] ?>">
<button type="submit" name="delete_permohonan" class="file-btn danger">
Hapus
</button>
</form>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>


<div id="previewModal" class="preview-modal" hidden>
<div class="preview-dialog">
<div class="preview-header">
<h3 id="previewTitle">Lihat File</h3>
<div class="preview-toolbar">
<a id="previewDownloadLink" class="modal-icon-btn" href="#" target="_blank" aria-label="Download file" title="Download">
<i data-feather="download"></i>
</a>
<button type="button" id="closePreview" class="modal-icon-btn" aria-label="Tutup preview" title="Tutup">
<i data-feather="x"></i>
</button>
</div>
</div>
<div id="previewBody" class="preview-body"></div>
</div>
</div>

</main>

<script src="assets/js/kelola_surat.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');

    tabLinks.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-target');

            tabLinks.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            tabContents.forEach(function (c) {
                c.style.display = (c.id === targetId) ? 'block' : 'none';
            });
        });
    });
});
</script>

</body>
</html>
