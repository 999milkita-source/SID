<?php
require_once '../config/config.php';
require_once '../config/auth.php';
ensure_session_started();
set_security_headers();
check_login();
require_role('rt');

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$user_rt = (int) $_SESSION['rt'];

// Ambil permohonan warga sesuai RT (TAB: Proses & Arsip)
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

$stmtArsip = $pdo->prepare("
SELECT p.id,p.jenis_surat,p.keterangan,p.status,p.created_at,
u.nama_lengkap,u.rt,u.rw
FROM permohonan p
JOIN users u ON p.user_id = u.id
WHERE u.rt = :rt
AND p.status != 'menunggu_rt'
ORDER BY p.created_at DESC
");
$stmtArsip->execute(['rt'=>$user_rt]);
$data_arsip = $stmtArsip->fetchAll(PDO::FETCH_ASSOC);

// proses verifikasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $_SESSION['error'] = 'Token CSRF tidak valid. Silakan muat ulang halaman lalu coba lagi.';
        header('Location: verifikasi.php');
        exit;
    }

    $id = (int)($_POST['id'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';
    $statusMap = [
        'setujui' => 'menunggu_rw',
        'tolak' => 'ditolak_rt',
    ];

    if ($id <= 0 || !isset($statusMap[$aksi])) {
        $_SESSION['error'] = 'Permintaan verifikasi tidak valid.';
        header('Location: verifikasi.php');
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE permohonan p
        JOIN users u ON p.user_id = u.id
        SET p.status = :status
        WHERE p.id = :id
          AND u.rt = :rt
          AND p.status = 'menunggu_rt'
    ");
    $stmt->execute([
        ':status' => $statusMap[$aksi],
        ':id' => $id,
        ':rt' => $user_rt,
    ]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = $aksi === 'setujui'
            ? 'Permohonan berhasil disetujui dan diteruskan ke RW.'
            : 'Permohonan berhasil ditolak.';
    } else {
        $_SESSION['error'] = 'Permohonan tidak ditemukan atau sudah diproses sebelumnya.';
    }

    header("Location: verifikasi.php");
    exit;
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi RT</title>
<link rel="stylesheet" href="assets/css/verifikasi_surat.css">
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  .tabs { display:flex; gap:.5rem; margin: .75rem 0 1rem; }
  .tab-link { border:0; padding:.5rem .9rem; cursor:pointer; border-radius:.5rem; background:#e5e7eb; color:#111827; }
  .tab-link.active { background:#2563eb; color:#fff; }
</style>
</head>
<body>

<?php include '../public/inc/sidebar.php'; ?>
<main class="content">
    <h2>Verifikasi Surat</h2>
    <div class="card">
    <div class="tabs">
        <button type="button" class="tab-link active" data-target="tab-proses">Proses</button>
        <button type="button" class="tab-link" data-target="tab-arsip">Arsip</button>
    </div>

<div id="tab-proses" class="tab-content" style="display:block;">
<table class="table">
<tr>
<th>Nama</th>
<th>Jenis Surat</th>
<th>Keterangan</th>
<th>Aksi</th>
</tr>

<?php if (empty($data)): ?>
<tr>
<td colspan="4" class="empty-state">Tidak ada permohonan yang menunggu verifikasi RT.</td>
</tr>
<?php else: foreach($data as $d): ?>
<tr>
<td><?= htmlspecialchars($d['nama_lengkap']) ?></td>
<td><?= htmlspecialchars($d['jenis_surat']) ?></td>
<td><?= htmlspecialchars($d['keterangan']) ?></td>
<td>
<form method="POST" class="verification-form">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
<input type="hidden" name="id" value="<?= $d['id'] ?>">
<div class="action-buttons">
<button
    type="submit"
    name="aksi"
    value="setujui"
    class="action-btn approve-btn js-confirm-action"
    data-confirm-title="Setujui permohonan ini?"
    data-confirm-text="Permohonan akan diteruskan ke RW untuk proses verifikasi berikutnya."
    data-confirm-icon="question"
    data-confirm-button="Ya, setujui"
    data-confirm-color="#16a34a"
>Setujui</button>
<button
    type="submit"
    name="aksi"
    value="tolak"
    class="action-btn reject-btn js-confirm-action"
    data-confirm-title="Tolak permohonan ini?"
    data-confirm-text="Permohonan akan ditandai ditolak oleh RT."
    data-confirm-icon="warning"
    data-confirm-button="Ya, tolak"
    data-confirm-color="#dc2626"
>Tolak</button>
</div>
</form>
</td>
</tr>
<?php endforeach; endif; ?>

</table>
</div>

<div id="tab-arsip" class="tab-content" style="display:none;">
<table class="table">
<tr>
<th>Nama</th>
<th>Jenis Surat</th>
<th>Keterangan</th>
<th>Status</th>
</tr>

<?php if (empty($data_arsip)): ?>
<tr>
<td colspan="4" class="empty-state">Belum ada arsip permohonan untuk wilayah RT Anda.</td>
</tr>
<?php else: foreach($data_arsip as $d): ?>
<tr>
<td><?= htmlspecialchars($d['nama_lengkap']) ?></td>
<td><?= htmlspecialchars($d['jenis_surat']) ?></td>
<td><?= htmlspecialchars($d['keterangan']) ?></td>
<td><?= htmlspecialchars(str_replace('_', ' ', $d['status'])) ?></td>
</tr>
<?php endforeach; endif; ?>

</table>
</div>

    </div>
</main>
<!-- Lightbox -->
<div id="lightbox" class="lightbox">
    <span class="close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
    <a id="lightbox-download" class="download-btn" href="#" download>Download</a>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const successMessage = <?= json_encode($success) ?>;
    const errorMessage = <?= json_encode($error) ?>;

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: successMessage
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorMessage
        });
    }

    document.querySelectorAll('.js-confirm-action').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();

            const form = button.form;
            if (!form) {
                return;
            }

            Swal.fire({
                icon: button.dataset.confirmIcon || 'question',
                title: button.dataset.confirmTitle || 'Lanjutkan aksi?',
                text: button.dataset.confirmText || 'Pastikan keputusan ini sudah benar.',
                showCancelButton: true,
                confirmButtonText: button.dataset.confirmButton || 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: button.dataset.confirmColor || '#2563eb',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit(button);
                    return;
                }

                const fallbackAction = form.querySelector('input[name="aksi"]') || document.createElement('input');
                fallbackAction.type = 'hidden';
                fallbackAction.name = 'aksi';
                fallbackAction.value = button.value;

                if (!fallbackAction.parentNode) {
                    form.appendChild(fallbackAction);
                }

                form.submit();
            });
        });
    });

    // Tab Proses/Arsip (show/hide)
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    tabLinks.forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            tabLinks.forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            tabContents.forEach((c) => {
                c.style.display = (c.id === targetId) ? 'block' : 'none';
            });
        });
    });
});
</script>
</body>
</html>
