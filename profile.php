<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user'])) header("Location: index.php");
$user = $_SESSION['user'];
$id_user = $user['id_user'];

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $level = $_POST['level'];
    $kontak = $_POST['kontak'];
    $foto = $user['foto'];

    if ($_FILES['foto']['name']) {
        $foto = 'img/' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }

    mysqli_query($conn, "UPDATE user SET nama='$nama', email='$email', level='$level', kontak='$kontak', foto='$foto' WHERE id_user=$id_user");
    $_SESSION['user'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE id_user=$id_user"));
    header("Location: profile.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="profile.css">
</head>
<body>
<div class="header">
    <div><i class="fas fa-user"></i> Profil</div>
    <div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="tugas.php"><i class="fas fa-tasks"></i> Tugas</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
<div class="profile-box">
    <img src="<?= $user['foto'] ?? 'img/default.jpg' ?>" class="profile-img" alt="Foto Profil">
    <h5 class="fw-bold"><?= $user['nama'] ?></h5>
    <small><?= $user['email'] ?></small>

    <div class="profile-info">
        <span><i class="fas fa-user"></i> PERAN</span><span><?= $user['level'] ?? '-' ?></span>
    </div>
    <div class="profile-info">
        <span><i class="fas fa-phone"></i> KONTAK</span><span><?= $user['kontak'] ?? '-' ?></span>
    </div>
    <div class="profile-info">
        <span><i class="fas fa-calendar-alt"></i> BERGABUNG</span><span><?= date('d - m - Y', strtotime($user['created_at'] ?? 'now')) ?></span>
    </div>

    <button class="btn-yellow mt-3" data-bs-toggle="modal" data-bs-target="#editModal">EDIT PROFILE</button>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nama" value="<?= $user['nama'] ?>" class="form-control mb-2" required>
        <input type="email" name="email" value="<?= $user['email'] ?>" class="form-control mb-2" required>
        <input type="text" name="level" value="<?= $user['level'] ?>" class="form-control mb-2" placeholder="Peran">
        <input type="text" name="kontak" value="<?= $user['kontak'] ?>" class="form-control mb-2" placeholder="Kontak">
        <label class="form-label">Foto Profil</label>
        <input type="file" name="foto" class="form-control">
      </div>
      <div class="modal-footer">
        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
