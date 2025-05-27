<?php
include 'db.php';
if (isset($_POST['reset'])) {
    $email = $_POST['email'];
    $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $cek = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE user SET password='$newpass' WHERE email='$email'");
        $success = "Password berhasil direset. Silakan login.";
    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/lupapass.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="reset-container">
    <div class="reset-left">
        <img src="img/forgot.png" alt="Reset Password Illustration">
    </div>
    <div class="reset-right">
        <h5 class="text-center mb-4 text-secondary"><i class="fas fa-unlock-alt"></i> Reset Password</h5>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="POST">
            <div class="mb-3 form-icon">
                <i class="fas fa-user"></i>
                <input type="email" name="email" class="form-control" placeholder="Konfirmasi Email" required>
            </div>
            <div class="mb-3 form-icon">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Password Baru" required>
            </div>
            <button type="submit" name="reset" class="btn btn-gradient w-100">Reset</button>
        </form>
    </div>
</div>
</body>
</html>
