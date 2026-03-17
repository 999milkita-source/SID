<?php
session_start();
require_once '../config/config.php';
require_once '_protect.php';

if ($_SESSION['role'] !== 'rw') {
    die("Akses ditolak");
}

$user_rw = (int) $_SESSION['rw'];

$stmt = $pdo->prepare("
SELECT p.*, u.nama_lengkap, u.rt, u.rw
FROM permohonan p
JOIN users u ON p.user_id = u.id
WHERE u.rw = :rw
AND p.status = 'menunggu_rw'
ORDER BY p.created_at DESC
");

$stmt->execute(['rw'=>$user_rw]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']==='POST'){
$id=(int)$_POST['id'];
$aksi=$_POST['aksi'];

if($aksi==='setujui'){
$pdo->prepare("UPDATE permohonan SET status='diproses_admin' WHERE id=?")->execute([$id]);
}else{
$pdo->prepare("UPDATE permohonan SET status='ditolak_rw' WHERE id=?")->execute([$id]);
}

header("Location: " . $_SERVER['PHP_SELF']);
exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Verifikasi RW</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<h2>Verifikasi Surat RW</h2>

<table border="1" cellpadding="5">
<tr>
<th>Nama</th>
<th>Jenis Surat</th>
<th>Keterangan</th>
<th>Aksi</th>
</tr>

<?php foreach($data as $d): ?>
<tr>
<td><?= htmlspecialchars($d['nama_lengkap']) ?></td>
<td><?= htmlspecialchars($d['jenis_surat']) ?></td>
<td><?= htmlspecialchars($d['keterangan']) ?></td>
<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="id" value="<?= $d['id'] ?>">
<button name="aksi" value="setujui">Setujui</button>
<button name="aksi" value="tolak">Tolak</button>
</form>
</td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>