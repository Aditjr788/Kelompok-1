<?php
include 'db.php';
if (isset($_POST['reset'])) {
    $email = $_POST['email'];
    $newpass_plain = $_POST['password'];

    // Gunakan prepared statement
    $stmt = mysqli_prepare($conn, "SELECT email FROM user WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $cek = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($cek) > 0) {
        $newpass_hash = password_hash($newpass_plain, PASSWORD_DEFAULT);
        $stmt_update = mysqli_prepare($conn, "UPDATE user SET password=? WHERE email=?");
        mysqli_stmt_bind_param($stmt_update, "ss", $newpass_hash, $email);
        mysqli_stmt_execute($stmt_update);
        $success = "Password berhasil direset. Silakan login.";
    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="lupapass.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="reset-container">
    <div class="reset-left">
        <img src="https://via.placeholder.com/400x400.png?text=Task+Flow" alt="Reset Password Illustration">
    </div>
    <div class="reset-right">
        <h5 class="text-center mb-4 text-secondary"><i class="fas fa-unlock-alt"></i> Reset Password</h5>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="POST">
            <div class="mb-3 form-icon">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Konfirmasi Email" required>
            </div>
            <div class="mb-3 form-icon">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Password Baru" required>
            </div>
            <button type="submit" name="reset" class="btn btn-gradient w-100">Reset</button>
            <p class="text-center mt-3"><a href="index.php">Kembali ke Login</a></p>
        </form>
    </div>
</div>
</body>
</html>