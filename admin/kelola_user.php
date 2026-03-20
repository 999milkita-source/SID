<?php
require_once '../config/config.php';
require_once '../config/auth.php';

check_login();
require_role('admin');

$success = $error = null;

/* ===============================
   HAPUS USER/PENDUDUK
================================ */
if (isset($_POST['hapus'])) {
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([(int)$_POST['hapus']]);
    $success = "Data berhasil dihapus";
}

/* ===============================
   SIMPAN (CREATE / UPDATE) VIA MODAL
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $mode = $_POST['mode'] ?? 'penduduk';
    $id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $nik          = trim($_POST['nik'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
    $alamat       = trim($_POST['alamat'] ?? '');
    $rt           = (int)($_POST['rt'] ?? 0);
    $rw           = (int)($_POST['rw'] ?? 0);
    $no_hp        = trim($_POST['no_hp'] ?? '');

    $email        = trim($_POST['email'] ?? '');
    $role         = $_POST['role'] ?? '';
    $password_raw = $_POST['password'] ?? '';

    if ($mode === 'penduduk') {
        if ($nik === '' || $nama_lengkap === '') {
            $error = "NIK dan Nama wajib diisi untuk data penduduk.";
        } else {
            if ($id <= 0) {
                $stmt = $pdo->prepare("
                    INSERT INTO users (nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, rt, rw, no_hp)
                    VALUES (:nik,:nama,:tmp,:tgl,:jk,:alamat,:rt,:rw,:hp)
                ");
                $stmt->execute([
                    ':nik'    => $nik,
                    ':nama'   => $nama_lengkap,
                    ':tmp'    => $tempat_lahir ?: null,
                    ':tgl'    => $tanggal_lahir ?: null,
                    ':jk'     => $jenis_kelamin ?: null,
                    ':alamat' => $alamat ?: null,
                    ':rt'     => $rt ?: null,
                    ':rw'     => $rw ?: null,
                    ':hp'     => $no_hp ?: null,
                ]);
                $success = "Penduduk berhasil ditambahkan.";
            } else {
                $stmt = $pdo->prepare("
                    UPDATE users SET
                        nik = :nik,
                        nama_lengkap = :nama,
                        tempat_lahir = :tmp,
                        tanggal_lahir = :tgl,
                        jenis_kelamin = :jk,
                        alamat = :alamat,
                        rt = :rt,
                        rw = :rw,
                        no_hp = :hp
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':nik'    => $nik,
                    ':nama'   => $nama_lengkap,
                    ':tmp'    => $tempat_lahir ?: null,
                    ':tgl'    => $tanggal_lahir ?: null,
                    ':jk'     => $jenis_kelamin ?: null,
                    ':alamat' => $alamat ?: null,
                    ':rt'     => $rt ?: null,
                    ':rw'     => $rw ?: null,
                    ':hp'     => $no_hp ?: null,
                    ':id'     => $id,
                ]);
                $success = "Data penduduk berhasil diperbarui.";
            }
        }
    } else { // mode user
        if ($email === '' || $role === '') {
            $error = "Email dan role wajib diisi untuk data user.";
        } elseif ($id <= 0) {
            $error = "Silakan pilih penduduk yang sudah terdaftar saat menambah user.";
        } else {
            $sql = "UPDATE users SET email = :email, role = :role";
            $params = [
                ':email' => $email,
                ':role'  => $role,
                ':id'    => $id,
            ];
            if (!empty($password_raw)) {
                $password = password_hash($password_raw, PASSWORD_DEFAULT);
                $sql .= ", password = :password";
                $params[':password'] = $password;
            }
            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = "Data user berhasil diperbarui.";
        }
    }
}

/* ===============================
   DATA USERS (DIPAKAI DI DUA TAB)
================================ */
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Penduduk & User</title>
<link rel="stylesheet" href="assets/css/kelola_user.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .tabs { display:flex; gap:.5rem; margin:.75rem 0 1rem; }
    .tab-link { border:0; padding:.5rem .9rem; cursor:pointer; border-radius:.5rem; background:#e5e7eb; color:#111827; }
    .tab-link.active { background:#2563eb; color:#fff; }
    .toolbar { margin-bottom:.75rem; }
</style>
</head>
<body>

<?php include '../public/inc/sidebar.php'; ?>

<main class="content">
<h2>Kelola Data Penduduk & User</h2>

<?php if($success): ?>
<script>
Swal.fire({icon:'success',title:'Berhasil',text:'<?= $success ?>'});
</script>
<?php endif; ?>

<?php if($error): ?>
<script>
Swal.fire({icon:'error',title:'Gagal',text:'<?= $error ?>'});
</script>
<?php endif; ?>

<div class="tabs">
    <button type="button" class="tab-link active" data-target="tab-penduduk">Data Penduduk</button>
    <button type="button" class="tab-link" data-target="tab-user">Data User</button>
</div>

<!-- TAB DATA PENDUDUK -->
<div id="tab-penduduk" class="tab-content" style="display:block;">
    <div class="toolbar">
        <button type="button" class="btn" onclick="openTambahPenduduk()">+ Tambah Penduduk</button>
    </div>

    <table>
    <tr>
    <th>NIK</th>
    <th>Nama</th>
    <th>TTL</th>
    <th>JK</th>
    <th>Alamat</th>
    <th>RT/RW</th>
    <th>No HP</th>
    <th>Aksi</th>
    </tr>

    <?php foreach($users as $u): ?>
    <tr>
    <td><?= htmlspecialchars($u['nik']) ?></td>
    <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
    <td><?= htmlspecialchars($u['tempat_lahir'].', '.$u['tanggal_lahir']) ?></td>
    <td><?= htmlspecialchars($u['jenis_kelamin']) ?></td>
    <td><?= htmlspecialchars($u['alamat']) ?></td>
    <td><?= htmlspecialchars($u['rt'].'/'.$u['rw']) ?></td>
    <td><?= htmlspecialchars($u['no_hp']) ?></td>
    <td>
        <button type="button" class="btn" onclick='openEditPenduduk(<?= json_encode($u) ?>)'>Edit</button>
        <form method="post" style="display:inline" class="form-hapus" data-nama="<?= htmlspecialchars($u['nama_lengkap']) ?>">
        <input type="hidden" name="hapus" value="<?= $u['id'] ?>">
        <button type="submit">Hapus</button>
        </form>
    </td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>

<!-- TAB DATA USER -->
<div id="tab-user" class="tab-content" style="display:none;">
    <div class="toolbar">
        <button type="button" class="btn" onclick="openTambahUser()">+ Tambah User</button>
    </div>

    <table>
    <tr>
    <th>Nama</th>
    <th>Email / Username</th>
    <th>Role</th>
    <th>Aksi</th>
    </tr>

    <?php foreach($users as $u): ?>
    <tr>
    <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= strtoupper($u['role']) ?></td>
    <td>
        <button type="button" class="btn" onclick='openEditUser(<?= json_encode($u) ?>)'>Edit</button>
        <form method="post" style="display:inline" class="form-hapus" data-nama="<?= htmlspecialchars($u['nama_lengkap']) ?>">
        <input type="hidden" name="hapus" value="<?= $u['id'] ?>">
        <button type="submit">Hapus</button>
        </form>
    </td>
    </tr>
    <?php endforeach; ?>
    </table>
</div>
</main>
<?php include 'modal_user.php'; ?>
<script src="assets/js/kelola_user.js"></script>

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

    // konfirmasi hapus dengan SweetAlert
    document.querySelectorAll('.form-hapus').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const nama = form.getAttribute('data-nama') || 'data ini';
            Swal.fire({
                icon: 'warning',
                title: 'Hapus data?',
                text: 'Anda akan menghapus ' + nama + '. Tindakan ini tidak dapat dibatalkan.',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // autocomplete nama penduduk (suggestion by huruf, tidak tampil semua)
    const pendudukList = <?php
        $sorted = $users;
        usort($sorted, function($a, $b) {
            return strcmp($a['nama_lengkap'] ?? '', $b['nama_lengkap'] ?? '');
        });
        $list = [];
        foreach ($sorted as $u) {
            $list[] = ['id' => (int)$u['id'], 'nama_lengkap' => $u['nama_lengkap']];
        }
        echo json_encode($list);
    ?>;

    function setupNamaAutocomplete(inputId, listId, updateHiddenId) {
        const input = document.getElementById(inputId);
        const listEl = document.getElementById(listId);
        const hiddenId = document.getElementById('id');
        if (!input || !listEl) return;

        function closeList() {
            listEl.style.display = 'none';
            listEl.innerHTML = '';
        }

        input.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            if (!query) {
                closeList();
                if (updateHiddenId && hiddenId) hiddenId.value = '';
                return;
            }
            const matches = pendudukList
                .filter(p => (p.nama_lengkap || '').toLowerCase().startsWith(query))
                .slice(0, 8); // batasi 8 teratas

            if (!matches.length) {
                closeList();
                if (updateHiddenId && hiddenId) hiddenId.value = '';
                return;
            }

            listEl.innerHTML = '';
            matches.forEach(p => {
                const li = document.createElement('li');
                li.textContent = p.nama_lengkap;
                li.addEventListener('click', function () {
                    input.value = p.nama_lengkap;
                    closeList();
                    if (updateHiddenId && hiddenId) hiddenId.value = p.id;
                });
                listEl.appendChild(li);
            });
            listEl.style.display = 'block';

            // jika user ketik tepat sama dengan salah satu nama
            if (updateHiddenId && hiddenId) {
                const exact = matches.find(p => (p.nama_lengkap || '').toLowerCase() === query);
                hiddenId.value = exact ? exact.id : '';
            }
        });

        document.addEventListener('click', function (e) {
            if (!listEl.contains(e.target) && e.target !== input) {
                closeList();
            }
        });
    }

    // mode penduduk: hanya saran visual, tidak update id
    setupNamaAutocomplete('nama_lengkap', 'namaPendudukSuggestions', false);
    // mode user: saran + set id user yang dipilih
    setupNamaAutocomplete('nama_lengkap_user', 'namaUserSuggestions', true);
});
</script>
</body>
</html>
