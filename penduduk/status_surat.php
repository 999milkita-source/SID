<?php
require_once '../config/config.php';
require_once '../config/auth.php';

ensure_session_started();
check_login();
require_role('penduduk');
$user_id = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare("
SELECT id, jenis_surat, status, file_upload, file_surat_admin, created_at 
FROM permohonan 
WHERE user_id=:uid 
ORDER BY created_at DESC
");
$stmt->execute(['uid'=>$user_id]);
$surat_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Surat - SID Wolokota</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/status_surat.css">
<script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>

<main class="content">
<h2>Status Surat</h2>
<div class="card">
<table class="table">
<tr>
<th>Jenis Surat</th>
<th>Status</th>
<th>Tanggal</th>
<th>Dokumen Pendukung</th>
<th>Surat Jadi</th>
</tr>
<?php if(empty($surat_list)): ?>
<tr><td colspan="5" style="text-align:center;">Belum ada permohonan surat</td></tr>
<?php else: foreach($surat_list as $s): ?>
<tr>

<td><?= htmlspecialchars($s['jenis_surat']) ?></td>

<td>
<span class="badge <?= strtolower($s['status']) ?>">
<?= str_replace('_',' ',$s['status']) ?>
</span>
</td>

<td><?= date('d-m-Y', strtotime($s['created_at'])) ?></td>

<td>
<?php if($s['file_upload']):
$files = array_filter(array_map('trim', explode(',', $s['file_upload']))); ?>

<div class="file-preview-grid">
<?php foreach($files as $f): ?>
<?= render_preview_tile($f) ?>
<?php endforeach; ?>
</div>

<?php else: ?>-<?php endif; ?>
</td>

<td>

<?php if(!empty($s['file_surat_admin'])): ?>

<div class="file-preview-grid">
<?= render_preview_tile($s['file_surat_admin']) ?>
</div>

<?php else: ?>

Belum tersedia

<?php endif; ?>

</td>

</tr><?php endforeach; endif; ?>
</table>
</div>
</main>

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

<script src="assets/js/ajukan_surat.js"></script>
<script src="assets/js/status_surat.js"></script>
</body>
</html>
