<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: index.php");
include 'db.php';
$user = $_SESSION['user'];
$id_user = $user['id_user'];

$berjalan = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tugas WHERE id_user = $id_user AND status = 'Berjalan'"));
$selesai = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tugas WHERE id_user = $id_user AND status = 'Selesai'"));
$tertunda = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tugas WHERE id_user = $id_user AND status = 'Tertunda'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="header">
    <h4>Manajemen Tugas</h4>
    <div>
        <a href="dashboard.php" class="text-white me-3"><i class="fas fa-home"></i> Home</a>
        <a href="tugas.php" class="text-white me-3"><i class="fas fa-tasks"></i> Tugas</a>
        <a href="profile.php" class="text-white me-3"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php" class="text-white"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
<div class="container mt-4">
    <h3 class="fw-bold">DASHBOARD</h3>
    <p><strong>Halo, Selamat Datang!</strong><br>Berikut adalah ringkasan tugas harianmu</p>

    <div class="status-card">
        <div><i class="fas fa-hourglass-half icon"></i> Tugas Berjalan</div>
        <span><?= $berjalan ?></span>
    </div>
    <div class="status-card" style="background-color: #28a745;">
        <div><i class="fas fa-check-circle icon"></i> Tugas Selesai</div>
        <span><?= $selesai ?></span>
    </div>
    <div class="status-card" style="background-color: #17a2b8;">
        <div><i class="fas fa-book icon"></i> Tugas Tertunda</div>
        <span><?= $tertunda ?></span>
    </div>
</div>
<div class="menu-bottom">
    <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="tugas.php"><i class="fas fa-tasks"></i> Tugas</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
</body>
</html>