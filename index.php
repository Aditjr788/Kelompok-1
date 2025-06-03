<?php
session_start();
include 'db.php';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);
    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['user'] = $data;
        header("Location: dashboard.php");
    } else {
        $error = "Login gagal! Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="login-container">
    <div class="login-left">
        <h2>Selamat Datang!</h2>
        <p><em>Task Flow</em> adalah aplikasi yang dirancang untuk membantu pengguna (baik individu maupun tim) dalam mengelola tugas sehari-hari dengan lebih terstruktur.</p>
    </div>
    <div class="login-right">
        <h5 class="text-center mb-4 text-secondary">USER LOGIN</h5>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3 form-icon">
                <i class="fas fa-user"></i>
                <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
            </div>
            <div class="mb-3 form-icon">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <a href="register.php">Buat Akun</a>
                <a href="lupapass.php">Lupa Password?</a>
            </div>
            <button type="submit" name="login" class="btn btn-gradient w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
