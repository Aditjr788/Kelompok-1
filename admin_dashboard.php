<?php
session_start();
// Periksa apakah pengguna sudah login dan levelnya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['level'] != 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';
$user = $_SESSION['user'];

// Menghitung total pengguna
$total_users_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user");
$total_users = mysqli_fetch_assoc($total_users_query)['total'];

// Menghitung total tugas dari semua pengguna
$total_tugas_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tugas");
$total_tugas = mysqli_fetch_assoc($total_tugas_query)['total'];

// Menghitung tugas yang kedaluwarsa dari semua pengguna
$total_kedaluwarsa_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tugas WHERE status = 'Kedaluwarsa'");
$total_kedaluwarsa = mysqli_fetch_assoc($total_kedaluwarsa_query)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .admin-header { background-color: #dc3545; }
        .admin-header a { color: white !important; }
        .admin-header a.active { font-weight: bold; text-decoration: underline; }
        .status-card { margin-top: 15px; }
    </style>
</head>
<body>
<div class="header admin-header"> 
    <h4>ADMIN PANEL - Task Flow</h4>
    <div>
        <a href="admin_dashboard.php" class="me-3 active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_manage_users.php" class="me-3"><i class="fas fa-users-cog"></i> Kelola Pengguna</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a> 
    </div>
</div>
<div class="container mt-4">
    <h3 class="fw-bold">ADMIN DASHBOARD</h3>
    <p><strong>Halo, Administrator <?php echo htmlspecialchars($user['nama']); ?>!</strong><br>Selamat datang di panel kontrol admin.</p>

    <div class="row">
        <div class="col-md-4">
            <div class="status-card" style="background-color: #007bff;">
                <div><i class="fas fa-users icon"></i> Total Pengguna</div>
                <span><?php echo $total_users; ?></span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="status-card" style="background-color: #17a2b8;">
                <div><i class="fas fa-tasks icon"></i> Total Semua Tugas</div>
                <span><?php echo $total_tugas; ?></span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="status-card" style="background-color: #ffc107; color: #333;">
                <div><i class="fas fa-clock icon"></i> Total Tugas Kedaluwarsa</div>
                <span><?php echo $total_kedaluwarsa; ?></span>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>