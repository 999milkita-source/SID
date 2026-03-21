    <?php
require_once '../../config/config.php';
    require_once '../../config/auth.php';

check_login();
require_role('admin');

    $judul = $_POST['judul'];
    $file = $_FILES['foto'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png','webp'];

    if(!in_array($ext,$allow)) die('Format tidak diizinkan');

    $dest_dir = __DIR__ . '/../../uploads/';
    if (!is_dir($dest_dir)) {
        mkdir($dest_dir, 0755, true);
    }
    $nama = uniqid().'.'.$ext;
    move_uploaded_file($file['tmp_name'], $dest_dir . $nama);

    $stmt = $pdo->prepare("INSERT INTO galeri (judul,file) VALUES (?,?)");
    $stmt->execute([$judul,$nama]);

    header("Location: info_desa.php?status=sukses_upload");
