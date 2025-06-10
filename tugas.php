<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: index.php"); 
    exit();
}

$user = $_SESSION['user']; 
$user_email = $user['email']; 
$is_admin = ($user['level'] == 'admin');
date_default_timezone_set('Asia/Jakarta');

// Tambah tugas
if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];
    $tenggat_waktu = !empty($_POST['tenggat_waktu']) ? $_POST['tenggat_waktu'] : null;
    $tgl_buat = date('Y-m-d');

    $stmt = mysqli_prepare($conn, "INSERT INTO tugas (judul_tugas, deskripsi, tanggal_buat, status, tenggat_waktu, user_email) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $judul, $deskripsi, $tgl_buat, $status, $tenggat_waktu, $user_email);
    mysqli_stmt_execute($stmt);
    header("Location: tugas.php?status_op=tambah_sukses");
    exit();
}

// Hapus tugas
if (isset($_GET['hapus'])) {
    $id_tugas_hapus = $_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM tugas WHERE id_tugas = ? AND (user_email = ? OR ? = 'admin')");
    mysqli_stmt_bind_param($stmt, "iss", $id_tugas_hapus, $user_email, $user['level']);
    mysqli_stmt_execute($stmt);
    header("Location: tugas.php?status_op=hapus_sukses");
    exit();
}

// Edit Tugas
if (isset($_POST['simpan_perubahan'])) {
    $id_tugas_edit = $_POST['id_tugas_edit'];
    $judul_edit = $_POST['judul_edit'];
    $deskripsi_edit = $_POST['deskripsi_edit'];
    $status_edit = $_POST['status_edit'];
    $tenggat_waktu_edit = !empty($_POST['tenggat_waktu_edit']) ? $_POST['tenggat_waktu_edit'] : null;

    $stmt = mysqli_prepare($conn, "UPDATE tugas SET judul_tugas=?, deskripsi=?, status=?, tenggat_waktu=? WHERE id_tugas=? AND (user_email=? OR ? = 'admin')");
    mysqli_stmt_bind_param($stmt, "ssssiss", $judul_edit, $deskripsi_edit, $status_edit, $tenggat_waktu_edit, $id_tugas_edit, $user_email, $user['level']);
    mysqli_stmt_execute($stmt);
    header("Location: tugas.php?status_op=edit_sukses");
    exit();
}

// Ambil data tugas untuk ditampilkan
if ($is_admin) {
    // Admin melihat semua tugas
    $tugas_query = mysqli_query($conn, "SELECT t.*, u.username FROM tugas t JOIN user u ON t.user_email = u.email ORDER BY t.tanggal_buat DESC");
} else {
    // User biasa hanya melihat tugasnya sendiri
    $stmt = mysqli_prepare($conn, "SELECT * FROM tugas WHERE user_email = ? ORDER BY tanggal_buat DESC");
    mysqli_stmt_bind_param($stmt, "s", $user_email);
    mysqli_stmt_execute($stmt);
    $tugas_query = mysqli_stmt_get_result($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Tugas - Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="tugas.css">
    <style>
      .badge-kedaluwarsa { background-color: #dc3545; }
    </style>
</head>
<body>
<div class="header">
    <h4>Daftar Tugas</h4>
    <div>
        <?php if ($is_admin): ?>
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
        <?php else: ?>
            <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <?php endif; ?>
        <a href="tugas.php"><i class="fas fa-tasks"></i> Tugas</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container mt-4" id="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold"><?php echo $is_admin ? "Semua Tugas Pengguna" : "Tugas Saya"; ?></h4>
        <button class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Tugas</button>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <?php if ($is_admin) echo '<th>Pengguna</th>'; ?>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Tenggat</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($tugas_query) > 0): ?>
            <?php while($t = mysqli_fetch_assoc($tugas_query)): ?>
                <tr>
                    <?php if ($is_admin) echo '<td>' . htmlspecialchars($t['username']) . '</td>'; ?>
                    <td><?php echo htmlspecialchars($t['judul_tugas']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($t['deskripsi'])); ?></td>
                    <td><?php echo $t['tenggat_waktu'] ? date('d M Y, H:i', strtotime($t['tenggat_waktu'])) : '-'; ?></td>
                    <td>
                        <?php
                        $status_class = 'badge-secondary';
                        if ($t['status'] == 'Berjalan') $status_class = 'badge-berjalan';
                        elseif ($t['status'] == 'Selesai') $status_class = 'badge-selesai';
                        elseif ($t['status'] == 'Tertunda') $status_class = 'badge-tertunda';
                        elseif ($t['status'] == 'Kedaluwarsa') $status_class = 'badge-kedaluwarsa';
                        echo '<span class="badge-status ' . $status_class . '">' . $t['status'] . '</span>';
                        ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary btn-edit-tugas"
                                data-bs-toggle="modal" data-bs-target="#modalEdit"
                                data-id="<?php echo $t['id_tugas']; ?>"
                                data-judul="<?php echo htmlspecialchars($t['judul_tugas']); ?>"
                                data-deskripsi="<?php echo htmlspecialchars($t['deskripsi']); ?>"
                                data-status="<?php echo $t['status']; ?>"
                                data-tenggat="<?php echo $t['tenggat_waktu']; ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="?hapus=<?php echo $t['id_tugas']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus tugas ini?')"><i class="fas fa-trash"></i> Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?php echo $is_admin ? '6' : '5'; ?>" class="text-center">Tidak ada tugas.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Tugas Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Judul Tugas</label>
            <input type="text" name="judul" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Tenggat Waktu (Opsional)</label>
            <input type="datetime-local" name="tenggat_waktu" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="Berjalan">Berjalan</option>
                <option value="Selesai">Selesai</option>
                <option value="Tertunda">Tertunda</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Tugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_tugas_edit" id="edit_id_tugas">
        <div class="mb-3">
            <label class="form-label">Judul Tugas</label>
            <input type="text" name="judul_edit" id="edit_judul" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi_edit" id="edit_deskripsi" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Tenggat Waktu (Opsional)</label>
            <input type="datetime-local" name="tenggat_waktu_edit" id="edit_tenggat" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status_edit" id="edit_status" class="form-select" required>
                <option value="Berjalan">Berjalan</option>
                <option value="Selesai">Selesai</option>
                <option value="Tertunda">Tertunda</option>
                <option value="Kedaluwarsa">Kedaluwarsa</option>
            </select>
        </div>
    </div>
      <div class="modal-footer">
        <button type="submit" name="simpan_perubahan" class="btn btn-success">Simpan Perubahan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEdit = document.getElementById('modalEdit');
    modalEdit.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('edit_id_tugas').value = button.getAttribute('data-id');
        document.getElementById('edit_judul').value = button.getAttribute('data-judul');
        document.getElementById('edit_deskripsi').value = button.getAttribute('data-deskripsi');
        document.getElementById('edit_status').value = button.getAttribute('data-status');
        // Format tanggal untuk input datetime-local
        let tenggat = button.getAttribute('data-tenggat');
        if (tenggat) {
            // Ubah 'YYYY-MM-DD HH:MM:SS' menjadi 'YYYY-MM-DDTHH:MM'
            document.getElementById('edit_tenggat').value = tenggat.slice(0, 16).replace(' ', 'T');
        } else {
            document.getElementById('edit_tenggat').value = '';
        }
    });
});
</script>
</body>
</html>