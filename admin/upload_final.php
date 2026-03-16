<?php
require_once '../admin/_protect.php';
require_once '../config/config.php';

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['file_surat'])){
    $permohonan_id = $_POST['permohonan_id'];
    $file = $_FILES['file_surat'];

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'surat_'.$permohonan_id.'_'.time().'.'.$ext;
    $path = '../storage/' . $filename;

    if(move_uploaded_file($file['tmp_name'], $path)){
        $stmt = $pdo->prepare("UPDATE permohonan SET status='final', file_upload=:file WHERE id=:id");
        $stmt->execute(['file'=>$filename, 'id'=>$permohonan_id]);
        $success = "File berhasil diupload";
    } else {
        $error = "Upload gagal";
    }
}

// Ambil surat diterima RW & belum final
$stmt = $pdo->prepare("SELECT p.*, u.nama_lengkap FROM permohonan p JOIN users u ON p.user_id=u.id WHERE status='diterima_rw'");
$stmt->execute();
$surat_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Upload Surat Final</h2>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<table border="1">
<tr><th>Nama</th><th>Jenis Surat</th><th>Keterangan</th><th>Upload</th></tr>
<?php foreach($surat_list as $s){ ?>
<tr>
    <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
    <td><?= htmlspecialchars($s['jenis_surat']) ?></td>
    <td><?= htmlspecialchars($s['keterangan']) ?></td>
    <td>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="permohonan_id" value="<?= $s['id'] ?>">
            <input type="file" name="file_surat" required>
            <button type="submit">Upload</button>
        </form>
    </td>
</tr>
<?php } ?>
</table>
