<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Jika admin, arahkan ke dashboard admin
if ($_SESSION['user']['level'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

include 'db.php';
$user = $_SESSION['user'];
$user_email = $user['email'];

// Fungsi untuk menghitung tugas berdasarkan status
function count_tugas($conn, $email, $status) {
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM tugas WHERE user_email = ? AND status = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result)['total'];
}

$berjalan = count_tugas($conn, $user_email, 'Berjalan');
$selesai = count_tugas($conn, $user_email, 'Selesai');
$tertunda = count_tugas($conn, $user_email, 'Tertunda');
$kedaluwarsa = count_tugas($conn, $user_email, 'Kedaluwarsa');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <p><strong>Halo, Selamat Datang <?php echo htmlspecialchars($user['nama']); ?>!</strong><br>Berikut adalah ringkasan tugas harianmu</p>
    
    <div class="status-card"> 
        <div><i class="fas fa-hourglass-half icon"></i> Tugas Berjalan</div>
        <span><?php echo $berjalan; ?></span> 
    </div>
    <div class="status-card" style="background-color: #28a745;"> 
        <div><i class="fas fa-check-circle icon"></i> Tugas Selesai</div> 
        <span><?php echo $selesai; ?></span> 
    </div>
    <div class="status-card" style="background-color: #17a2b8;"> 
        <div><i class="fas fa-pause-circle icon"></i> Tugas Tertunda</div> 
        <span><?php echo $tertunda; ?></span> 
    </div>
     <div class="status-card" style="background-color: #dc3545;"> 
        <div><i class="fas fa-exclamation-triangle icon"></i> Tugas Kedaluwarsa</div> 
        <span><?php echo $kedaluwarsa; ?></span> 
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