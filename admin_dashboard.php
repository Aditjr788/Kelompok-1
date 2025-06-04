<?php
session_start();
// Periksa apakah pengguna sudah login dan levelnya admin
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['level']) || $_SESSION['user']['level'] != 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';
$user = $_SESSION['user'];
$id_user = $user['id_user']; 

//menghitung total pengguna
$total_users_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user");
$total_users_data = mysqli_fetch_assoc($total_users_query);
$total_users = $total_users_data['total'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="dashboard.css"> <style>
        .admin-header {
            background-color: #dc3545; /* Warna header admin yang berbeda */
        }
        .admin-header a {
            color: white !important;
        }
    </style>
</head>
<body>
<div class="header admin-header"> 
    <h4>ADMIN PANEL - Task Flow</h4>
    <div>
        <a href="admin_dashboard.php" class="me-3"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <a href="tugas.php" class="me-3"><i class="fas fa-tasks"></i> Tugas</a> <a href="profile.php" class="me-3"><i class="fas fa-user"></i> Profil</a> <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a> </div>
</div>
<div class="container mt-4">
    <h3 class="fw-bold">ADMIN DASHBOARD</h3>
    <p><strong>Halo, Administrator <?= htmlspecialchars($user['nama']); ?>!</strong><br>Selamat datang di panel kontrol admin.</p>

    <div class="row">
        <div class="col-md-4">
            <div class="status-card" style="background-color: #007bff;">
                <div><i class="fas fa-users icon"></i> Total Pengguna Terdaftar</div>
                <span><?= $total_users ?></span>
            </div>
        </div>
        </div>
    <p class="mt-3"></p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>