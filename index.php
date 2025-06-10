<?php
session_start();
include 'db.php';

// Jika sudah login, langsung arahkan ke dashboard yang sesuai
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['level'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}


if (isset($_POST['login'])) {
    // ==========================================================
    // PERBAIKAN: Menggunakan EMAIL untuk login, bukan username
    // ==========================================================
    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $_POST['email']);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    
    $data = mysqli_fetch_assoc($query); 

    if ($data && password_verify($_POST['password'], $data['password'])) { 
        $_SESSION['user'] = $data; 

        if ($data['level'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        // Pesan error juga disesuaikan
        $error = "Login gagal! Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="login-container">
    <div class="login-left">
        <h2>Selamat Datang!</h2>
        <p><em>Task Flow</em> adalah aplikasi yang dirancang untuk membantu Anda mengelola tugas sehari-hari dengan lebih terstruktur.</p> 
    </div>
    <div class="login-right">
        <h5 class="text-center mb-4 text-secondary">USER LOGIN</h5> 
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?> 
        <form method="POST">
            <div class="mb-3 form-icon">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required> 
            </div>
            <div class="mb-3 form-icon">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required> 
            </div>
            <div class="d-flex justify-content-between mb-3">
                <a href="register.php">Buat Akun</a> <a href="lupapass.php">Lupa Password?</a> 
            </div>
            <button type="submit" name="login" class="btn btn-gradient w-100">Login</button> 
        </form>
    </div>
</div>
</body>
</html>