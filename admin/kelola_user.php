<?php
require_once '../config/config.php';
require_once '_protect.php';

$success = $error = null;

/* ===============================
   SIMPAN (CREATE / UPDATE)
================================ */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['simpan'])) {

    $id = $_POST['id'] ?? '';

    $data = [
        'nik' => trim($_POST['nik']),
        'nama_lengkap' => trim($_POST['nama_lengkap']),
        'tempat_lahir' => trim($_POST['tempat_lahir']),
        'tanggal_lahir' => $_POST['tanggal_lahir'],
        'jenis_kelamin' => $_POST['jenis_kelamin'],
        'alamat' => trim($_POST['alamat']),
        'rt' => (int)$_POST['rt'],
        'rw' => (int)$_POST['rw'],
        'no_hp' => trim($_POST['no_hp']),
        'email' => trim($_POST['email']),
        'role' => $_POST['role']
    ];

    if ($data['nik']=='' || $data['nama_lengkap']=='') {
        $error = "NIK dan Nama wajib diisi!";
    } else {

        if ($id=='') {
            // CREATE
            $stmt = $pdo->prepare("
                INSERT INTO users
                (nik,nama_lengkap,tempat_lahir,tanggal_lahir,jenis_kelamin,alamat,rt,rw,no_hp,email,password,role)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([
                $data['nik'],$data['nama_lengkap'],$data['tempat_lahir'],
                $data['tanggal_lahir'],$data['jenis_kelamin'],$data['alamat'],
                $data['rt'],$data['rw'],$data['no_hp'],$data['email'],
                password_hash($_POST['password'] ?: '123456', PASSWORD_DEFAULT),
                $data['role']
            ]);
            $success = "Penduduk berhasil ditambahkan";
        } else {
            // UPDATE
$sql = "UPDATE users SET
    nik=?, nama_lengkap=?, tempat_lahir=?, tanggal_lahir=?,
    jenis_kelamin=?, alamat=?, rt=?, rw=?, no_hp=?, email=?, role=?";
$params = array_values($data);

/* =============================
   CEK JIKA GANTI PASSWORD
============================= */
if (!empty($_POST['password'])) {
    $sql .= ", password=?";
    $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
}

/* =============================
   JALANKAN UPDATE
============================= */
if (!isset($error)) {
    $sql .= " WHERE id=?";
    $params[] = $id;

    $pdo->prepare($sql)->execute($params);
    $success = "Data berhasil diperbarui";
}
        }
    }
}

/* ===============================
   HAPUS
================================ */
if (isset($_POST['hapus'])) {
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([(int)$_POST['hapus']]);
    $success = "Data berhasil dihapus";
}

/* ===============================
   DATA
================================ */
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Penduduk</title>
<link rel="stylesheet" href="assets/css/kelola_user.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include '../public/inc/sidebar.php'; ?>

<main class="content">
<h2>Kelola Data Penduduk</h2>

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

<button class="btn" onclick="openTambah()">+ Tambah Penduduk</button>

<table>
<tr>
<th>NIK</th>
<th>Nama</th>
<th>TTL</th>
<th>JK</th>
<th>Alamat</th>
<th>RT/RW</th>
<th>No HP</th>
<th>Email</th>
<th>Role</th>
<th>Aksi</th>
</tr>

<?php foreach($users as $u): ?>
<tr>
<td><?= $u['nik'] ?></td>
<td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
<td><?= $u['tempat_lahir'].', '.$u['tanggal_lahir'] ?></td>
<td>
<?= $u['jenis_kelamin'] == 'L' ? 'Laki-laki' : ($u['jenis_kelamin'] == 'P' ? 'Perempuan' : '-') ?>
</td>
<td><?= htmlspecialchars($u['alamat']) ?></td>
<td><?= $u['rt'].'/'.$u['rw'] ?></td>
<td><?= $u['no_hp'] ?></td>
<td><?= $u['email'] ?></td>
<td><?= strtoupper($u['role']) ?></td>
<td>
    
<button onclick='openEdit(<?= json_encode($u, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Edit</button>
<form method="post" style="display:inline">
<input type="hidden" name="hapus" value="<?= $u['id'] ?>">
<button onclick="return confirm('Hapus data?')">Hapus</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</main>

<?php include 'modal_user.php'; ?>
<script src="assets/js/kelola_user.js"></script>
</body>
</html>
