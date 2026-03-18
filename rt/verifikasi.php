<?php
require_once '../config/config.php';
require_once '../config/auth.php';
ensure_session_started();
set_security_headers();
check_login();
require_role('rt');

$csrf_token = generate_csrf_token();
$user_rt = (int) $_SESSION['rt'];

// Ambil permohonan warga sesuai RT
$stmt = $pdo->prepare("
SELECT p.id,p.jenis_surat,p.keterangan,p.status,p.created_at,
u.nama_lengkap,u.rt,u.rw
FROM permohonan p
JOIN users u ON p.user_id = u.id
WHERE u.rt = :rt
AND p.status = 'menunggu_rt'
ORDER BY p.created_at DESC
");

$stmt->execute(['rt'=>$user_rt]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// proses verifikasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        http_response_code(400);
        exit('Token CSRF tidak valid.');
    }

    $id = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';

    if ($id > 0) {
        if ($aksi === 'setujui') {
            $stmt = $pdo->prepare("UPDATE permohonan SET status = :status WHERE id = :id");
            $stmt->execute([':status' => 'menunggu_rw', ':id' => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE permohonan SET status = :status WHERE id = :id");
            $stmt->execute([':status' => 'ditolak_rt', ':id' => $id]);
        }
    }

    header("Location: verifikasi.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Verifikasi RT</title>
<link rel="stylesheet" href="assets/css/verifikasi_surat.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
    <h2>Verifikasi Surat</h2>
    <div class="card">
        
<table class="table">
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

    </div>
</main>
<!-- Lightbox -->
<div id="lightbox" class="lightbox">
    <span class="close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
    <a id="lightbox-download" class="download-btn" href="#" download>Download</a>
</div>

</body>
</html>