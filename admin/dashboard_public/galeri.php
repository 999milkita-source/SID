<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('admin');

if (isset($_GET['status']) && $_GET['status'] === 'sukses_upload') {
    echo '<div style="background:#d4edda;color:#155724;padding:12px;border-radius:4px;margin-bottom:20px;text-align:center;font-weight:500;">✅ Upload galeri berhasil!</div>';
}

$data = $pdo->query("SELECT * FROM galeri ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin - Kelola Galeri</title>
  <link rel="stylesheet" href="../assets/css/info_desa.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../../public/inc/sidebar.php'; ?>

<main class="content">
  <section class="card header-card">
    <h2>Kelola Galeri Foto Desa</h2>
    <button class="btn-primary" onclick="openAdd()">+ Tambah Foto</button>
  </section>

  <section class="card">
    <table class="table">
      <thead>
        <tr>
          <th>Gambar</th>
          <th>Judul</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($data as $g): ?>
          <tr>
            <td>
              <img src="../../uploads/<?= htmlspecialchars($g['file']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:4px" onerror="this.alt=\'Gambar tidak ditemukan\';this.src=\'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIGZpbGw9IiM5Q0E0QjIiIHN0cm9rZT0iI0JBQkJCRSIVPjxyZWN0IHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgZmlsbD0iIzlDQTVCQSIgc3Ryb2tlPSIjQkFCQkJFIi8+PHRleHQgeD0iMzAiIHk9IjMyIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTAiIGZpbGw9IndoaXRlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+5YqhPC90ZXh0Pjwvc3ZnPg==\'">
            </td>
            <td><?= htmlspecialchars($g['judul']) ?></td>
            <td>
              <button class="btn-edit" onclick='openEdit(<?= json_encode($g) ?>)'>Edit</button>
              <a class="btn-delete" href="hapus_galeri.php?id=<?= $g['id'] ?>">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($data)): ?>
          <tr>
            <td colspan="3" style="text-align:center;color:#6b7280;padding:3rem">
              Belum ada foto. Tambah pertama di atas.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</main>

<!-- Modal Add/Edit -->
<div id="modal" class="modal">
  <div class="modal-box">
    <h3 id="modal-title">Tambah Foto Galeri</h3>
    <form method="post" action="upload_galeri.php" enctype="multipart/form-data">
      <input type="hidden" name="id" id="id">
      
      <label>Judul Foto</label>
      <input type="text" name="judul" id="judul" required maxlength="100">

      <label>Gambar Baru (JPG/PNG/WEBP)</label>
      <input type="file" name="foto" id="foto" accept="image/*" required>
      <img id="preview-img" style="width:120px;height:80px;object-fit:cover;display:none;margin-top:10px;border-radius:6px;border:1px solid #ddd;">
      
      <div class="modal-action">
        <button type="submit" class="btn-primary">Upload / Update</button>
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="../assets/js/info_desa.js"></script>
<script>
// Galeri specific
window.openAdd = function() {
  document.getElementById('modal-title').textContent = 'Tambah Foto Galeri';
  document.querySelector('#modal form').reset();
  document.getElementById('id').value = '';
  document.getElementById('foto').value = '';
  document.getElementById('foto').setAttribute('required', 'required');
  document.getElementById('preview-img').style.display = 'none';
  document.getElementById('modal').style.display = 'flex';
};

window.openEdit = function(data) {
  document.getElementById('modal-title').textContent = 'Edit Foto Galeri';
  document.getElementById('id').value = data.id;
  document.getElementById('judul').value = data.judul;
  document.getElementById('foto').value = '';
  document.getElementById('foto').removeAttribute('required');
  document.getElementById('preview-img').style.display = 'none';
  document.getElementById('modal').style.display = 'flex';
};
  document.addEventListener('DOMContentLoaded', function() {
    const fotoInput = document.getElementById('foto');
    const preview = document.getElementById('preview-img');
    fotoInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
          preview.src = ev.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        preview.style.display = 'none';
      }
    });
  });

</script>
</body>
</html>

