<?php
require_once '../config/config.php';
require_once '../config/auth.php';
ensure_session_started();
set_security_headers();
check_login();
require_role('rw');

$csrf_token = generate_csrf_token();
$user_rw = (int) $_SESSION['rw'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        http_response_code(400);
        exit('Token CSRF tidak valid.');
    }

    $id = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';
    if ($id > 0) {
        $status = $aksi === 'setujui' ? 'diproses_admin' : 'ditolak_rw';
        $stmt = $pdo->prepare("UPDATE permohonan SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
    }

    header('Location: verifikasi.php');
    exit;
}

$stmt = $pdo->prepare("SELECT p.id,p.jenis_surat,p.keterangan,p.status,p.created_at,u.nama_lengkap,u.rt,u.rw FROM permohonan p JOIN users u ON p.user_id = u.id WHERE u.rw = :rw AND p.status = 'menunggu_rw' ORDER BY p.created_at DESC");
$stmt->execute(['rw' => $user_rw]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi RW</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/verifikasi_surat.css">
</head>
<body>
<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
    <h2>Verifikasi Surat RW</h2>
    <div class="card">
        <table class="table">
            <tr>
                <th>Nama</th>
                <th>Jenis Surat</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            <?php if (empty($data)): ?>
            <tr><td colspan="5" style="text-align:center;">Tidak ada permohonan menunggu verifikasi RW.</td></tr>
            <?php else: foreach ($data as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($d['jenis_surat']) ?></td>
                <td><?= htmlspecialchars($d['keterangan']) ?></td>
                <td><?= htmlspecialchars(str_replace('_', ' ', $d['status'])) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                        <button name="aksi" value="setujui">Setujui</button>
                        <button name="aksi" value="tolak">Tolak</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; endif; ?>
        </table>
    </div>
</main>
</body>
</html>
