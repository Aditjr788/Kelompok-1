<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: index.php"); 
    exit();
}

$user = $_SESSION['user']; 
$user_email = $user['email']; 

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    // Email tidak bisa diedit karena menjadi Primary Key
    $username_baru = mysqli_real_escape_string($conn, $_POST['username']);
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
    $foto_path = $user['foto']; 

    // Handle upload foto
    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != '') { 
        $target_dir = "img/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $foto_filename = uniqid() . '-' . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $foto_filename;
        
        // Hapus foto lama jika bukan default
        if ($user['foto'] && $user['foto'] != 'img/default.jpg' && file_exists($user['foto'])) {
            unlink($user['foto']);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto_path = $target_file; 
        } else {
            // Abaikan jika upload gagal
        }
    }

    // Update data pengguna, gunakan prepared statement
    $stmt = mysqli_prepare($conn, "UPDATE user SET nama=?, username=?, kontak=?, foto=? WHERE email=?");
    mysqli_stmt_bind_param($stmt, "sssss", $nama, $username_baru, $kontak, $foto_path, $user_email);
    mysqli_stmt_execute($stmt);
    
    // Update session dengan data baru
    $stmt_refresh = mysqli_prepare($conn, "SELECT * FROM user WHERE email = ?");
    mysqli_stmt_bind_param($stmt_refresh, "s", $user_email);
    mysqli_stmt_execute($stmt_refresh);
    $_SESSION['user'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_refresh));
    
    header("Location: profile.php"); 
    exit();
}

$is_admin = ($user['level'] == 'admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="header"> 
    <div><i class="fas fa-user-circle"></i> Profil</div>
    <div>
        <?php if ($is_admin): ?>
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <?php else: ?>
             <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <?php endif; ?>
        <a href="tugas.php"><i class="fas fa-tasks"></i> Tugas</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
<div class="profile-box">
    <img src="<?php echo htmlspecialchars($user['foto'] ?? 'img/default.jpg'); ?>" class="profile-img" alt="Foto Profil">
    <h5 class="fw-bold"><?php echo htmlspecialchars($user['nama']); ?></h5>
    <small><?php echo htmlspecialchars($user['email']); ?></small>
    <div class="profile-info"> <span><i class="fas fa-user-tag"></i> PERAN</span><span><?php echo htmlspecialchars(ucfirst($user['level'])); ?></span> </div>
    <div class="profile-info"> <span><i class="fas fa-phone"></i> KONTAK</span><span><?php echo htmlspecialchars($user['kontak'] ?? '-'); ?></span> </div>
    <div class="profile-info"> <span><i class="fas fa-calendar-alt"></i> BERGABUNG</span><span><?php echo date('d M Y', strtotime($user['created_at'])); ?></span> </div>

    <button class="btn-yellow mt-3" data-bs-toggle="modal" data-bs-target="#editModal">EDIT PROFILE</button>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
            <label class="form-label">Email (Tidak bisa diubah)</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" readonly>
        </div>
        <div class="mb-2">
            <label class="form-label">Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label">Kontak</label>
            <input type="text" name="kontak" value="<?php echo htmlspecialchars($user['kontak']); ?>" class="form-control" placeholder="Kontak">
        </div>
        <div class="mb-2">
            <label class="form-label">Foto Profil</label>
            <input type="file" name="foto" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>