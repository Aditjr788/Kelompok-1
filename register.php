<?php
include 'db.php';
if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = $username;
    mysqli_query($conn, "INSERT INTO user (nama, email, username, password) VALUES ('$nama','$email','$username','$password')");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="regist.css">

</head>
<body>
<div class="register-box">
    <h3 class="text-center fw-bold">Buat akun</h3>
    <p class="text-center text-muted">Buat akun gratis</p>
    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required>
        </div>
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
        </div>
        <button name="register" class="btn btn-gradient w-100">Buat Akun</button>
    </form>
</div>
</body>
</html>
