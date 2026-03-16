<?php
session_start();
require_once '../config/config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("
        SELECT id, email, password, role, nama_lengkap
        FROM users
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
if($user && password_verify($password, $user['password'])){
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['rt'] = $user['rt'];
    $_SESSION['rw'] = $user['rw'];

    $_SESSION['fingerprint'] = hash(
        'sha256',
        $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']
    );

    switch($user['role']){
        case 'penduduk':
            header('Location: ../penduduk/index.php'); break;
        case 'rt':
            header('Location: ../rt/index.php'); break;
        case 'rw':
            header('Location: ../rw/index.php'); break;
        case 'kades':
            header('Location: ../kades/index.php'); break;
        default:
            session_destroy();
            die('Role tidak valid');
    }
    exit;
}

    else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login SID Wolokota</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
<div class="publiclogin">
    <div class="formlogin">
        <h2>Login</h2>
        <?php if(isset($error)): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</div>
</body>
</html>
