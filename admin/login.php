<?php
require_once '../secure/admin_guard.php';
require_once '../config/session.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $password = $_POST['password'] ?? '';
    if(admin_login($password)){
        header('Location: index.php');
        exit;
    } else {
        $error = "Password admin salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin SID Wolokota</title>
    <link rel="stylesheet" href="../admin/assets/css/login.css">
</head>
<body>
    <div class="publiclogin">
        <div class="formlogin">
<h2>Login Admin SID Wolokota</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST" action="">
    <input type="password" name="password" placeholder="Password Admin" required>
    <button type="submit">Login</button>
</form>
</div>
</div>
</body>
</html>
