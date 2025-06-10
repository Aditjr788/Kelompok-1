<?php
session_start();
require 'db.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['user']) || $_SESSION['user']['level'] != 'admin') {
    header("Location: index.php");
    exit();
}

$current_admin_email = $_SESSION['user']['email'];
$message = '';
$message_type = '';

// Proses Hapus Pengguna
if (isset($_GET['hapus'])) {
    $email_to_delete = $_GET['hapus'];
    // Admin tidak bisa menghapus akunnya sendiri
    if ($email_to_delete === $current_admin_email) {
        $message = "Error: Admin tidak dapat menghapus akunnya sendiri.";
        $message_type = "danger";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM user WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Pengguna berhasil dihapus.";
            $message_type = "success";
        } else {
            $message = "Gagal menghapus pengguna.";
            $message_type = "danger";
        }
    }
}


// Proses Edit Pengguna (saat form disubmit)
if (isset($_POST['simpan_perubahan'])) {
    $email_edit = $_POST['email_edit'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $level = $_POST['level'];
    $kontak = $_POST['kontak'];

    // Jika admin mengubah levelnya sendiri menjadi user, tolak perubahan
    if ($email_edit === $current_admin_email && $level !== 'admin') {
         $message = "Error: Anda tidak dapat mengubah level akun Anda sendiri dari admin menjadi user.";
         $message_type = "danger";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE user SET username = ?, nama = ?, level = ?, kontak = ? WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "sssss", $username, $nama, $level, $kontak, $email_edit);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Data pengguna berhasil diperbarui.";
            $message_type = "success";
        } else {
            $message = "Gagal memperbarui data pengguna. Username mungkin sudah ada yang pakai.";
            $message_type = "danger";
        }
    }
}


// Ambil semua data pengguna untuk ditampilkan
$result = mysqli_query($conn, "SELECT email, username, nama, level, kontak FROM user");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="header admin-header"> 
    <h4>ADMIN PANEL - Task Flow</h4>
    <div>
        <a href="admin_dashboard.php" class="me-3"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_manage_users.php" class="me-3 active"><i class="fas fa-users-cog"></i> Kelola Pengguna</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a> 
    </div>
</div>

<div class="container mt-4">
    <h3 class="fw-bold">Kelola Pengguna</h3>
    <p>Di halaman ini, Anda dapat mengedit dan menghapus data pengguna.</p>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Level</th>
                    <th>Kontak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['level']); ?></td>
                    <td><?php echo htmlspecialchars($row['kontak']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                data-email="<?php echo htmlspecialchars($row['email']); ?>" 
                                data-username="<?php echo htmlspecialchars($row['username']); ?>" 
                                data-nama="<?php echo htmlspecialchars($row['nama']); ?>"
                                data-level="<?php echo htmlspecialchars($row['level']); ?>"
                                data-kontak="<?php echo htmlspecialchars($row['kontak']); ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <?php if ($row['email'] !== $current_admin_email): // Tombol hapus tidak muncul untuk admin sendiri ?>
                        <a href="?hapus=<?php echo htmlspecialchars($row['email']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua tugas yang terkait juga akan terhapus.')">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="admin_manage_users.php">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit Pengguna</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="email_edit" id="edit-email">
            <div class="mb-3">
              <label for="edit-username" class="form-label">Username</label>
              <input type="text" class="form-control" id="edit-username" name="username" required>
            </div>
            <div class="mb-3">
              <label for="edit-nama" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="edit-nama" name="nama">
            </div>
            <div class="mb-3">
              <label for="edit-kontak" class="form-label">Kontak</label>
              <input type="text" class="form-control" id="edit-kontak" name="kontak">
            </div>
            <div class="mb-3">
              <label for="edit-level" class="form-label">Level</label>
              <select class="form-select" id="edit-level" name="level" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="simpan_perubahan" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var editModal = document.getElementById('editUserModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        // Ambil data dari atribut data-*
        document.getElementById('edit-email').value = button.getAttribute('data-email');
        document.getElementById('edit-username').value = button.getAttribute('data-username');
        document.getElementById('edit-nama').value = button.getAttribute('data-nama');
        document.getElementById('edit-level').value = button.getAttribute('data-level');
        document.getElementById('edit-kontak').value = button.getAttribute('data-kontak');
    });
});
</script>
</body>
</html>