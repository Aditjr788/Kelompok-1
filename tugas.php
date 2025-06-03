<?php
session_start();
include 'db.php';
$user = $_SESSION['user'];
if (!isset($user)) header("Location: index.php");
$id_user = $user['id_user'];

// Tambah tugas
if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];
    $tgl = date('Y-m-d');
    mysqli_query($conn, "INSERT INTO tugas (judul_tugas, deskripsi, tanggal_buat, status, id_user) VALUES ('$judul', '$deskripsi', '$tgl', '$status', '$id_user')");
    header("Location: tugas.php");
}

// Hapus tugas
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tugas WHERE id_tugas=$id AND id_user=$id_user");
    header("Location: tugas.php");
}

$tugas = mysqli_query($conn, "SELECT * FROM tugas WHERE id_user=$id_user");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="tugas.css">
</head>
<body>
<div class="header">
    <h4>Task Flow</h4>
    <div>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="tugas.php"><i class="fas fa-tasks"></i> Tugas</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Daftar Tugas</h4>
        <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Tugas</button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while($t = mysqli_fetch_assoc($tugas)) { ?>
            <tr>
                <td><?= $t['judul_tugas'] ?></td>
                <td><?= $t['deskripsi'] ?></td>
                <td><?= $t['tanggal_buat'] ?></td>
                <td>
                    <?php if ($t['status'] == 'Berjalan') echo '<span class="badge-status badge-berjalan">Berjalan</span>';
                    elseif ($t['status'] == 'Selesai') echo '<span class="badge-status badge-selesai">Selesai</span>';
                    else echo '<span class="badge-status badge-tertunda">Tertunda</span>'; ?>
                </td>
                <td>
                    <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                    <a href="?hapus=<?= $t['id_tugas'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus tugas ini?')"><i class="fas fa-trash"></i> Hapus</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Tugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="judul" placeholder="Judul Tugas" class="form-control mb-2" required>
        <textarea name="deskripsi" placeholder="Deskripsi" class="form-control mb-2" required></textarea>
        <select name="status" class="form-select" required>
            <option value="Berjalan">Berjalan</option>
            <option value="Selesai">Selesai</option>
            <option value="Tertunda">Tertunda</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
