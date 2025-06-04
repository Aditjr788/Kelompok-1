<?php
session_start();
include 'db.php'; 
$user = $_SESSION['user']; 
if (!isset($user)) {
    header("Location: index.php"); 
    exit();
}
$id_user = $user['id_user']; 
$is_admin = (isset($_SESSION['user']['level']) && $_SESSION['user']['level'] == 'admin');

// Tambah tugas
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $tgl = date('Y-m-d');
    mysqli_query($conn, "INSERT INTO tugas (judul_tugas, deskripsi, tanggal_buat, status, id_user) VALUES ('$judul', '$deskripsi', '$tgl', '$status', '$id_user')"); //
    header("Location: tugas.php?tambah=sukses");
    exit();
}

// Hapus tugas
if (isset($_GET['hapus'])) {
    $id_tugas_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    // Cek kepemilikan atau admin sebelum hapus
    $q_check_owner_hapus = mysqli_query($conn, "SELECT id_user FROM tugas WHERE id_tugas = '$id_tugas_hapus'");
    if ($q_check_owner_hapus && mysqli_num_rows($q_check_owner_hapus) > 0) {
        $task_owner_data_hapus = mysqli_fetch_assoc($q_check_owner_hapus);
        if ($task_owner_data_hapus['id_user'] == $id_user || $is_admin) {
            mysqli_query($conn, "DELETE FROM tugas WHERE id_tugas='$id_tugas_hapus'"); //
            header("Location: tugas.php?hapus=sukses");
            exit();
        } else {
            header("Location: tugas.php?hapus=gagal_hak");
            exit();
        }
    } else {
        header("Location: tugas.php?hapus=gagal_tidak_ditemukan");
        exit();
    }
}

// Edit Tugas
if (isset($_POST['simpan_perubahan'])) {
    $id_tugas_edit = mysqli_real_escape_string($conn, $_POST['id_tugas_edit']);
    $judul_edit = mysqli_real_escape_string($conn, $_POST['judul_edit']);
    $deskripsi_edit = mysqli_real_escape_string($conn, $_POST['deskripsi_edit']);
    $status_edit = mysqli_real_escape_string($conn, $_POST['status_edit']);

    $q_check_owner_edit = mysqli_query($conn, "SELECT id_user FROM tugas WHERE id_tugas = '$id_tugas_edit'");
    if ($q_check_owner_edit && mysqli_num_rows($q_check_owner_edit) > 0) {
        $task_owner_data_edit = mysqli_fetch_assoc($q_check_owner_edit);
        if ($task_owner_data_edit['id_user'] == $id_user || $is_admin) {
            mysqli_query($conn, "UPDATE tugas SET judul_tugas='$judul_edit', deskripsi='$deskripsi_edit', status='$status_edit' WHERE id_tugas='$id_tugas_edit'");
            header("Location: tugas.php?edit=sukses");
            exit();
        } else {
            header("Location: tugas.php?edit=gagal_hak");
            exit();
        }
    } else {
        header("Location: tugas.php?edit=gagal_tidak_ditemukan");
        exit();
    }
}

$tugas_query_string = "SELECT * FROM tugas WHERE id_user=$id_user ORDER BY tanggal_buat DESC, id_tugas DESC"; //

$tugas = mysqli_query($conn, $tugas_query_string);

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

<div class="container mt-4" id="main-content"> <div class="d-flex justify-content-between align-items-center mb-3">
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
                        <button type="button" class="btn btn-sm btn-primary btn-edit-tugas"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit"
                                data-id="<?= $t['id_tugas'] ?>"
                                data-judul="<?= htmlspecialchars($t['judul_tugas']) ?>"
                                data-deskripsi="<?= htmlspecialchars($t['deskripsi']) ?>"
                                data-status="<?= $t['status'] ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <?php if ($t['id_user'] == $id_user || $is_admin): ?>
                        <a href="?hapus=<?= $t['id_tugas'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus tugas ini?')"><i class="fas fa-trash"></i> Hapus</a> <?php endif; ?>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Tidak ada tugas.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true"> <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Tugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="tambah_judul" class="form-label">Judul Tugas</label>
            <input type="text" name="judul" id="tambah_judul" placeholder="Judul Tugas" class="form-control" required> </div>
        <div class="mb-3">
            <label for="tambah_deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="tambah_deskripsi" placeholder="Deskripsi" class="form-control" rows="3" required></textarea> </div>
        <div class="mb-3">
            <label for="tambah_status" class="form-label">Status</label>
            <select name="status" id="tambah_status" class="form-select" required> <option value="Berjalan">Berjalan</option>
                <option value="Selesai">Selesai</option>
                <option value="Tertunda">Tertunda</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambah" class="btn btn-success">Simpan</button> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditLabel">Edit Tugas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_tugas_edit" id="edit_id_tugas">
        
        <div class="mb-3">
            <label for="edit_judul" class="form-label">Judul Tugas</label>
            <input type="text" name="judul_edit" id="edit_judul" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi_edit" id="edit_deskripsi" class="form-control" rows="3" required></textarea>
        </div>
        
        <div class="mb-3">
            <label for="edit_status" class="form-label">Status</label>
            <select name="status_edit" id="edit_status" class="form-select" required>
                <option value="Berjalan">Berjalan</option>
                <option value="Selesai">Selesai</option>
                <option value="Tertunda">Tertunda</option>
            </select>
        </div>
    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" name="simpan_perubahan" class="btn btn-success">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEdit = document.getElementById('modalEdit');
    if (modalEdit) {
        modalEdit.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; 
            var idTugas = button.getAttribute('data-id');
            var judulTugas = button.getAttribute('data-judul');
            var deskripsiTugas = button.getAttribute('data-deskripsi');
            var statusTugas = button.getAttribute('data-status');

            var inputIdTugas = modalEdit.querySelector('#edit_id_tugas');
            var inputJudul = modalEdit.querySelector('#edit_judul');
            var textareaDeskripsi = modalEdit.querySelector('#edit_deskripsi');
            var selectStatus = modalEdit.querySelector('#edit_status');

            inputIdTugas.value = idTugas;
            inputJudul.value = judulTugas;
            textareaDeskripsi.value = deskripsiTugas;
            selectStatus.value = statusTugas;
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const pesanStatusCrud = {
        'tambah': urlParams.get('tambah'),
        'edit': urlParams.get('edit'),
        'hapus': urlParams.get('hapus')
    };

    let alertHTML = '';
    for (const [operasi, status] of Object.entries(pesanStatusCrud)) {
        if (status) {
            let pesan = '';
            let tipeAlert = 'info';
            switch (operasi) {
                case 'tambah': 
                    if (status === 'sukses') { pesan = 'Tugas berhasil ditambahkan.'; tipeAlert = 'success'; }
                    break;
                case 'edit':
                    if (status === 'sukses') { pesan = 'Tugas berhasil diperbarui.'; tipeAlert = 'success'; }
                    else if (status === 'gagal_hak') { pesan = 'Anda tidak berhak mengedit tugas ini.'; tipeAlert = 'danger'; }
                    else if (status === 'gagal_tidak_ditemukan') { pesan = 'Tugas yang akan diedit tidak ditemukan.'; tipeAlert = 'danger'; }
                    break;
                case 'hapus':
                    if (status === 'sukses') { pesan = 'Tugas berhasil dihapus.'; tipeAlert = 'success'; }
                    else if (status === 'gagal_hak') { pesan = 'Anda tidak berhak menghapus tugas ini.'; tipeAlert = 'danger'; }
                    else if (status === 'gagal_tidak_ditemukan') { pesan = 'Tugas yang akan dihapus tidak ditemukan.'; tipeAlert = 'danger'; }
                    break;
            }
            if (pesan) {
                alertHTML += `<div class="alert alert-${tipeAlert} alert-dismissible fade show" role="alert">${pesan}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
            }
        }
    }

    if (alertHTML) {
        const alertPlaceholder = document.createElement('div');
        alertPlaceholder.innerHTML = alertHTML;
        const mainContent = document.getElementById('main-content');
        if (mainContent) {
            mainContent.insertBefore(alertPlaceholder, mainContent.firstChild);
        }

        if (history.pushState) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.pushState({path:cleanUrl},'',cleanUrl);
        }
    }
});
</script>
</body>
</html>