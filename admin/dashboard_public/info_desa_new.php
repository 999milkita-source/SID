<?php
require_once __DIR__ . '/../../config/config.php';
require_once '../../config/auth.php';

ensure_session_started();
set_security_headers();
check_login();
require_role('admin');

$data = $pdo->query(
  "SELECT * FROM info_desa ORDER BY tipe, urutan ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$grouped = [];
foreach($data as $d){
  $grouped[$d['tipe']][] = $d;
}

$label = [
  'beranda' => 'Beranda',
  'profil'  => 'Profil Desa',
  'tentang' => 'Tentang Desa',
  'kontak' => 'Kontak',
  'galeri' => 'Galeri Foto',
  'background' => 'Background Halaman'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin - Info Desa</title>
<link rel="stylesheet" href="../assets/css/info_desa.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../../public/inc/sidebar.php'; ?>

<main class="content">

<section class="card header-card">
  <h2>Kelola Informasi Desa</h2>
  <button class="btn-primary" onclick="openAdd()">+ Tambah Konten</button>
</section>

<?php foreach($grouped as $tipe => $rows): ?>
<section class="card section-card">
  <h3><?= $label[$tipe] ?? strtoupper($tipe) ?></h3>

  <table class="table">
    <thead>
      <tr>
        <th>Judul</th>
        <th>Urutan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($rows as $d): ?>
      <tr>
        <td><?= htmlspecialchars($d['judul']) ?></td>
        <td><?= (int)$d['urutan'] ?></td>
        <td>
          <button class="btn-edit"
            onclick='openEdit(<?= json_encode($d, JSON_HEX_TAG) ?>)'>
            Edit
          </button>
          <a class="btn-delete"
             href="hapus_info.php?id=<?= $d['id'] ?>">
            Hapus
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

</section>
<?php endforeach; ?>

  <!-- GALERI SECTION -->
  <?php
  $galeri_data = $pdo->query("SELECT * FROM galeri ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
  ?>
  <section class="card section-card">
    <h3><?= $label['galeri'] ?? 'Galeri' ?></h3>
    
    <form method="post" action="upload_galeri.php" enctype="multipart/form-data" class="upload-form">
      <input name="judul" placeholder="Judul foto" required style="width:200px;">
      <input type="file" name="foto" accept="image/*" required>
      <button type="submit" class="btn-primary">Upload</button>
    </form>

    <?php if(empty($galeri_data)): ?>
      <p style="text-align:center;color:#64748b;">Belum ada foto galeri.</p>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-top:1rem;">
      <?php foreach($galeri_data as $g): ?>
        <div class="galeri-preview">
          <img src="../../uploads/<?= htmlspecialchars($g['file']) ?>" style="width:100%;border-radius:12px;height:150px;object-fit:cover;">
          <p><?= htmlspecialchars($g['judul']) ?></p>
          <div>
          <a href="hapus_galeri.php?id=<?= $g['id'] ?>" class="btn-delete" onclick="return confirm('Hapus?')">Hapus</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>

</main>

<!-- MODAL -->
<div id="modal" class="modal">
  <div class="modal-box">
    <h3>Form Informasi Desa</h3>

    <form method="post" action="simpan_info.php" enctype="multipart/form-data" id="infoForm">
      <input type="hidden" name="id" id="id">
      <input type="hidden" name="konten" id="konten">

      <label>Judul</label>
      <input type="text" name="judul" id="judul" required>

      <label>Tipe Halaman</label>
      <select name="tipe" id="tipe" required>
        <option value="beranda">Beranda</option>
        <option value="profil">Profil Desa</option>
        <option value="tentang">Tentang Desa</option>
        <option value="kontak">Kontak</option>
        <option value="background">Background Halaman</option>
        <option value="galeri">Galeri Foto</option>
      </select>

      <label>Urutan</label>
      <input type="number" name="urutan" id="urutan" value="0">

      <!-- Background Specific Fields -->
      <div id="backgroundFields" style="display:none;">
        <label>Pilihan Background:</label>
        <div style="display:flex;gap:1rem;margin-bottom:1rem;">
          <label><input type="radio" name="bgType" value="warna" checked onchange="toggleBgType()"> Warna (#hex)</label>
          <label><input type="radio" name="bgType" value="gambar" onchange="toggleBgType()"> Gambar (upload)</label>
        </div>

        <!-- Warna Mode -->
        <div id="warnaFields">
          <label>Warna Utama <span id="warnaPreview" style="display:inline-block;width:40px;height:20px;border:1px solid #ccc;border-radius:4px;margin-left:10px;"></span></label>
          <input type="color" id="color1" value="#ecfeff" onchange="updateKonten()">
          <label style="margin-left:2rem;">Warna Gradient 2 (opsional)</label>
          <input type="color" id="color2" value="#f8fafc" onchange="updateKonten()">
          <small>Kosongkan warna2 untuk solid color</small>
        </div>

        <!-- Gambar Mode -->
        <div id="gambarFields" style="display:none;">
          <label>Upload Gambar Background</label>
          <input type="file" name="background_foto" id="background_foto" accept="image/*">
          <small>Gambar akan disimpan di /uploads/ dan digunakan sebagai background full page</small>
          <div id="gambarPreview" style="margin-top:1rem;"></div>
        </div>

        <div style="margin-top:1rem;padding:1rem;background:#f0f9ff;border-radius:8px;font-family:monospace;font-size:12px;">
          <strong>Preview Konten (akan disimpan):</strong><br>
          <span id="kontenPreview">-</span>
        </div>
      </div>

      <!-- Regular Konten -->
      <div id="kontenWrap">
        <label>Konten</label>
        <textarea name="visible_konten" id="visible_konten" rows="6" placeholder="Masukkan teks/html untuk halaman..."></textarea>
      </div>

      <div class="modal-action">
        <button type="submit" class="btn-primary">Simpan</button>
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAdd() {
  document.getElementById('modal').style.display = 'flex';
  document.getElementById('id').value = '';
  document.getElementById('judul').value = '';
  document.getElementById('tipe').value = 'beranda';
  document.getElementById('urutan').value = 0;
  document.getElementById('visible_konten').value = '';
  document.getElementById('konten').value = '';
  toggleBgFields();
}

function openEdit(data) {
  document.getElementById('modal').style.display = 'flex';
  document.getElementById('id').value = data.id;
  document.getElementById('judul').value = data.judul;
  document.getElementById('tipe').value = data.tipe;
  document.getElementById('urutan').value = data.urutan;
  document.getElementById('visible_konten').value = data.konten;
  document.getElementById('konten').value = data.konten;
  toggleBgFields();
  // Try parse for preview
  setTimeout(updateKonten, 100);
}

function toggleBgFields() {
  const isBg = document.getElementById('tipe').value === 'background';
  document.getElementById('backgroundFields').style.display = isBg ? 'block' : 'none';
  document.getElementById('kontenWrap').style.display = isBg ? 'none' : 'block';
  if (isBg) toggleBgType();
}

function toggleBgType() {
  const isWarna = document.querySelector('input[name="bgType"]:checked').value === 'warna';
  document.getElementById('warnaFields').style.display = isWarna ? 'block' : 'none';
  document.getElementById('gambarFields').style.display = isWarna ? 'none' : 'block';
  updateKonten();
}

function updateKonten() {
  const isBg = document.getElementById('tipe').value === 'background';
  if (!isBg) return;

  const isWarna = document.querySelector('input[name="bgType"]:checked').value === 'warna';
  const c1 = document.getElementById('color1').value;
  const c2 = document.getElementById('color2').value;
  
  let kontenVal;
  if (isWarna) {
    // Simple: #hex for solid, or gradient
    if (c2 && c2 !== '#f8fafc') { // default, treat as empty
      kontenVal = `linear-gradient(135deg, ${c1}, ${c2})`;
    } else {
      kontenVal = c1; // simple #hex
    }
    // Update preview color
    document.getElementById('warnaPreview').style.background = kontenVal;
  } else {
    // Gambar: wait for file or show example
    kontenVal = `url('../uploads/example.jpg') center / cover no-repeat fixed`;
  }
  
  document.getElementById('konten').value = kontenVal;
  document.getElementById('kontenPreview').textContent = kontenVal;
}

// Event listeners
document.getElementById('tipe').addEventListener('change', toggleBgFields);
document.querySelectorAll('input[name="bgType"]').forEach(el => el.addEventListener('change', toggleBgType));

// Close modal
function closeModal() {
  document.getElementById('modal').style.display = 'none';
}
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal')) closeModal();
});

// File preview for gambar
document.getElementById('background_foto').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const url = URL.createObjectURL(file);
    const prev = document.getElementById('gambarPreview');
    prev.innerHTML = `<img src="${url}" style="max-width:200px;max-height:100px;border-radius:8px;">`;
    updateKonten();
  }
});
</script>
</body>
</html>
