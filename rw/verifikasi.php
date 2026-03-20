<?php
require_once '../config/config.php';
require_once '../config/auth.php';
ensure_session_started();
set_security_headers();
check_login();
require_role('rw');

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$user_rw = (int) $_SESSION['rw'];

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
        'setujui' => 'diproses_admin',
        'tolak' => 'ditolak_rw',
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
          AND u.rw = :rw
          AND p.status = 'menunggu_rw'
    ");
    $stmt->execute([
        ':status' => $statusMap[$aksi],
        ':id' => $id,
        ':rw' => $user_rw,
    ]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = $aksi === 'setujui'
            ? 'Permohonan berhasil disetujui dan diteruskan ke admin.'
            : 'Permohonan berhasil ditolak.';
    } else {
        $_SESSION['error'] = 'Permohonan tidak ditemukan atau sudah diproses sebelumnya.';
    }

    header('Location: verifikasi.php');
    exit;
}

$csrf_token = generate_csrf_token();

// Data TAB Proses (status menunggu_rw)
$stmt = $pdo->prepare("SELECT p.id,p.jenis_surat,p.keterangan,p.status,p.created_at,u.nama_lengkap,u.rt,u.rw FROM permohonan p JOIN users u ON p.user_id = u.id WHERE u.rw = :rw AND p.status = 'menunggu_rw' ORDER BY p.created_at DESC");
$stmt->execute(['rw' => $user_rw]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Data TAB Arsip (selain menunggu_rw)
$stmtArsip = $pdo->prepare("SELECT p.id,p.jenis_surat,p.keterangan,p.status,p.created_at,u.nama_lengkap,u.rt,u.rw FROM permohonan p JOIN users u ON p.user_id = u.id WHERE u.rw = :rw AND p.status != 'menunggu_rw' ORDER BY p.created_at DESC");
$stmtArsip->execute(['rw' => $user_rw]);
$data_arsip = $stmtArsip->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Verifikasi RW</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/verifikasi_surat.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                data-confirm-text="Permohonan akan diteruskan ke admin untuk proses berikutnya."
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
                                data-confirm-text="Permohonan akan ditandai ditolak oleh RW."
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
</main>
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
});
</script>
</body>
</html>
