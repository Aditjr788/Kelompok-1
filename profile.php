<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) {
    header("Location: index.php"); 
    exit();
}
$user = $_SESSION['user']; 
$id_user = $user['id_user']; 

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $level_input = mysqli_real_escape_string($conn, $_POST['level']); 
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
    $foto = $user['foto']; 

    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != '') { 
        $target_dir = "img/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $foto_filename = uniqid() . '-' . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $foto_filename;
        
        if ($user['foto'] && $user['foto'] != 'img/default.jpg' && file_exists($user['foto'])) {
            unlink($user['foto']);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) { //
            $foto = $target_file; 
        } else {
            // Handle error upload
            echo "Sorry, there was an error uploading your file.";
            $foto = $user['foto']; // Kembali ke foto lama jika gagal upload
        }
    }

    mysqli_query($conn, "UPDATE user SET nama='$nama', email='$email', level='$level_input', kontak='$kontak', foto='$foto' WHERE id_user=$id_user"); //
    
    // Update session
    $_SESSION['user'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE id_user=$id_user")); //
    header("Location: profile.php"); 
    exit();
}

// Cek apakah pengguna adalah admin
$is_admin = (isset($_SESSION['user']['level']) && $_SESSION['user']['level'] == 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="profile.css"> <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> </head>
<body>
<div class="header"> <div><i class="fas fa-user"></i> Profil</div>
    <div>
        <?php if ($is_admin): ?>
            <a href="admin_dashboard.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <?php endif; ?>
        <a href="dashboard.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-home"></i> Home</a> <a href="tugas.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-tasks"></i> Tugas</a> <a href="logout.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-sign-out-alt"></i> Logout</a> </div>
</div>
<div class="profile-box"> <img src="<?= htmlspecialchars($user['foto'] ?? 'img/default.jpg') ?>" class="profile-img" alt="Foto Profil"> <h5 class="fw-bold"><?= htmlspecialchars($user['nama']) ?></h5> <small><?= htmlspecialchars($user['email']) ?></small> <div class="profile-info"> <span><i class="fas fa-user"></i> PERAN</span><span><?= htmlspecialchars($user['level'] ?? '-') ?></span> </div>
    <div class="profile-info"> <span><i class="fas fa-phone"></i> KONTAK</span><span><?= htmlspecialchars($user['kontak'] ?? '-') ?></span> </div>
    <div class="profile-info"> <span><i class="fas fa-calendar-alt"></i> BERGABUNG</span><span><?= date('d - m - Y', strtotime($user['created_at'] ?? 'now')) ?></span> </div>

    <button class="btn-yellow mt-3" data-bs-toggle="modal" data-bs-target="#editModal">EDIT PROFILE</button> </div>

<div class="modal fade" id="editModal" tabindex="-1"> <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" class="form-control mb-2" required> <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control mb-2" required> <input type="text" name="level" value="<?= htmlspecialchars($user['level']) ?>" class="form-control mb-2" placeholder="Peran"> <input type="text" name="kontak" value="<?= htmlspecialchars($user['kontak']) ?>" class="form-control mb-2" placeholder="Kontak"> <label class="form-label">Foto Profil</label>
        <input type="file" name="foto" class="form-control"> </div>
      <div class="modal-footer">
        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> </body>
</html>