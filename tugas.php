<?php
session_start();
include 'db.php'; 
$user = $_SESSION['user']; 
if (!isset($user)) {
    header("Location: index.php"); 
    exit();
}
$id_user = $user['id_user']; 

// Tambah tugas
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $tgl = date('Y-m-d');
    mysqli_query($conn, "INSERT INTO tugas (judul_tugas, deskripsi, tanggal_buat, status, id_user) VALUES ('$judul', '$deskripsi', '$tgl', '$status', '$id_user')"); //
    header("Location: tugas.php"); 
    exit();
}

// Hapus tugas
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM tugas WHERE id_tugas=$id AND id_user=$id_user"); 
    header("Location: tugas.php"); 
    exit();
}

$tugas_query_string = "SELECT * FROM tugas WHERE id_user=$id_user"; 

$tugas = mysqli_query($conn, $tugas_query_string);

// Cek apakah pengguna adalah admin
$is_admin = (isset($_SESSION['user']['level']) && $_SESSION['user']['level'] == 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <link rel="stylesheet" href="tugas.css"> </head>
<body>
<div class="header">
    <h4>Task Flow</h4>
    <div>
        <?php if ($is_admin): ?>
            <a href="admin_dashboard.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <?php endif; ?>
        <a href="dashboard.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-home"></i> Home</a> <a href="tugas.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-tasks"></i> Tugas</a> <a href="profile.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-user"></i> Profil</a> <a href="logout.php" style="margin-left: 15px; color: white; text-decoration: none;"><i class="fas fa-sign-out-alt"></i> Logout</a> </div>
</div>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Daftar Tugas</h4>
        <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Tugas</button> </div>

    <table class="table table-bordered"> <thead>
            <tr>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($tugas) > 0): ?>
            <?php while($t = mysqli_fetch_assoc($tugas)) { ?> <tr>
                    <td><?= htmlspecialchars($t['judul_tugas']) ?></td> <td><?= nl2br(htmlspecialchars($t['deskripsi'])) ?></td> <td><?= htmlspecialchars($t['tanggal_buat']) ?></td> <td>
                        <?php 
                        //
                        if ($t['status'] == 'Berjalan') echo '<span class="badge-status badge-berjalan">Berjalan</span>';
                        elseif ($t['status'] == 'Selesai') echo '<span class="badge-status badge-selesai">Selesai</span>';
                        else echo '<span class="badge-status badge-tertunda">Tertunda</span>'; 
                        ?>
                    </td>
                    <td>
                        <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a> <?php if ($t['id_user'] == $id_user || $is_admin): ?>
                        <a href="?hapus=<?= $t['id_tugas'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus tugas ini?')"><i class="fas fa-trash"></i> Hapus</a> <?php endif; ?>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr>
                <td colspan="">Tidak ada tugas.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true"> <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Tugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="judul" placeholder="Judul Tugas" class="form-control mb-2" required> <textarea name="deskripsi" placeholder="Deskripsi" class="form-control mb-2" required></textarea> <select name="status" class="form-select" required> <option value="Berjalan">Berjalan</option>
            <option value="Selesai">Selesai</option>
            <option value="Tertunda">Tertunda</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambah" class="btn btn-success">Simpan</button> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> </body>
</html>