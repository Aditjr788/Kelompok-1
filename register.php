<?php
include 'db.php';
$error = '';
$success = '';

if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Validasi dasar
    if (empty($email) || empty($username) || empty($password)) {
        $error = "Semua field wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        // Cek apakah email atau username sudah ada
        $stmt_check = mysqli_prepare($conn, "SELECT email FROM user WHERE email = ? OR username = ?");
        mysqli_stmt_bind_param($stmt_check, "ss", $email, $username);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            $error = "Email atau Username sudah terdaftar!";
        } else {
            // Hash password dan masukkan ke DB
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $nama = $username; // Secara default, nama = username
            $level = 'user';

            $stmt_insert = mysqli_prepare($conn, "INSERT INTO user (email, username, nama, password, level) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "sssss", $email, $username, $nama, $hashed_password, $level);
            
            if (mysqli_stmt_execute($stmt_insert)) {
                $success = "Registrasi berhasil! Silakan login.";
                header("Refresh:2; url=index.php"); // Arahkan ke login setelah 2 detik
            } else {
                $error = "Registrasi gagal, silakan coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="regist.css"> 
</head>
<body>
<div class="register-box">
    <h3 class="text-center fw-bold">Buat Akun</h3>
    <p class="text-center text-muted">Buat akun gratis untuk mulai</p>
    
    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required> 
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required> 
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required> 
        </div>
        <button name="register" class="btn btn-gradient w-100">Buat Akun</button> 
        <p class="text-center mt-3">Sudah punya akun? <a href="index.php">Login di sini</a></p>
    </form>
</div>
</body>
</html>