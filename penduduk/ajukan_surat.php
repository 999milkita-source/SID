<?php
session_start();
require_once '../config/config.php';
require_once '_protect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$success = $error = null;

// PROSES SUBMIT SURAT
if($_SERVER['REQUEST_METHOD']==='POST'){
    $jenis_surat = trim($_POST['jenis_surat'] ?? '');
    $keperluan   = trim($_POST['keperluan'] ?? '');
    $tujuan      = trim($_POST['tujuan'] ?? '');
    $keterangan  = trim($_POST['keterangan'] ?? '');

    $file_uploads = [];
    if(isset($_FILES['file_upload'])){
        foreach($_FILES['file_upload']['tmp_name'] as $key=>$tmpPath){
            if($_FILES['file_upload']['error'][$key]===UPLOAD_ERR_OK){
                $fileName = $_FILES['file_upload']['name'][$key];
                $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $fileSize = $_FILES['file_upload']['size'][$key];
                $allowed  = ['pdf','jpg','jpeg','png'];
                $maxSize  = 5*1024*1024;
                if(!in_array($fileExt,$allowed)){ $error="Tipe file tidak diperbolehkan"; break; }
                if($fileSize>$maxSize){ $error="Ukuran file maksimal 5MB"; break; }

                $newFileName = uniqid().'_'.$fileName;
                if(move_uploaded_file($tmpPath,'../storage/'.$newFileName)){
                    $file_uploads[] = $newFileName;
                } else { $error="Gagal upload file $fileName"; break; }
            }
        }
    }

    $file_upload = implode(',',$file_uploads);

    if(!$error && $jenis_surat && $keperluan){
        try{
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("
INSERT INTO permohonan 
(user_id, jenis_surat, keterangan, file_upload, status) 
VALUES (:uid,:jenis,:ket,:file,'menunggu_rt')
");
            $stmt->execute([
                'uid'=>$user_id,
                'jenis'=>$jenis_surat,
                'ket'=>"Keperluan: $keperluan | Tujuan: $tujuan | $keterangan",
                'file'=>$file_upload
            ]);
            $permohonan_id = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO laporan (permohonan_id) VALUES (:pid)")->execute(['pid'=>$permohonan_id]);
            $pdo->commit();
            $success="Surat berhasil diajukan dan menunggu persetujuan RT.";
        } catch(Exception $e){ $pdo->rollBack(); $error="Gagal mengajukan surat."; }
    } elseif(!$error){ $error="Jenis surat dan keperluan wajib diisi."; }
}

/* Ambil data surat sebelumnya */
$stmt2 = $pdo->prepare("SELECT jenis_surat,status,file_upload,created_at FROM permohonan WHERE user_id=:uid ORDER BY created_at DESC");
$stmt2->execute(['uid'=>$user_id]);
$surat_list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ajukan Surat - SID Wolokota</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
<h1>Ajukan Surat</h1>
<p class="subtitle">Ajukan permohonan surat baru dan unggah dokumen pendukung</p>

<?php if($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
<?php if($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

<form id="form_surat" method="POST" enctype="multipart/form-data">
<label>Jenis Surat</label>
<select name="jenis_surat" id="jenis_surat" required>
<option value="">-- Pilih --</option>
<option value="Surat Keterangan Domisili">Surat Keterangan Domisili</option>
<option value="Surat Keterangan Usaha">Surat Keterangan Usaha</option>
<option value="Surat Keterangan Tidak Mampu">Surat Keterangan Tidak Mampu</option>
</select>
<p id="ket_surat" style="font-style:italic;color:#555;"></p>

<label>Keperluan Surat</label>
<input type="text" name="keperluan" required placeholder="misal: syarat KIP">
<label>Tujuan / Instansi</label>
<input type="text" name="tujuan" placeholder="misal: SMA, SMP">
<label>Keterangan Tambahan</label>
<textarea name="keterangan" placeholder="Opsional..."></textarea>

<label>Upload Dokumen Pendukung (maks 5MB per file)</label>
<input type="file" name="file_upload[]" id="file_upload" accept=".pdf,.jpg,.jpeg,.png" multiple>
<div id="file_preview"></div>

<button type="submit">Ajukan Surat</button>
</form>

<?php if(!empty($surat_list)): ?>
<div class="card-table">
<h2>Riwayat Permohonan</h2>
<table>
<tr>
<th>Jenis Surat</th>
<th>Status</th>
<th>Tanggal</th>
<th>File</th>
</tr>
<?php foreach($surat_list as $s): ?>
<tr>
<td><?= htmlspecialchars($s['jenis_surat']) ?></td>
<td><span class="badge <?= strtolower($s['status']) ?>"><?= str_replace('_',' ',$s['status']) ?></span></td>
<td><?= date('d-m-Y', strtotime($s['created_at'])) ?></td>
<td>
<?php if($s['file_upload']): $files=explode(',',$s['file_upload']); ?>
<?php foreach($files as $f): ?>
<a class="btn-download" href="../storage/<?= rawurlencode($f) ?>" target="_blank"><?= htmlspecialchars($f) ?></a><br>
<?php endforeach; ?>
<?php else: ?>-<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php endif; ?>

</main>
<script src="assets/js/ajukan_surat.js"></script>
</body>
</html>
