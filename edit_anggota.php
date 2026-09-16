<?php
session_start();
include 'config/koneksi.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data anggota lama berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM anggota WHERE id = '$id'");
if(mysqli_num_rows($query) == 0){
    echo "<script>alert('Data anggota tidak ditemukan!'); window.location='anggota.php';</script>";
    exit;
}
$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background:#111827; }
        .form-control { background-color: #1f2937; color: white; border: 1px solid #374151; }
        .form-control:focus { background-color: #374151; color: white; border-color: #3b82f6; box-shadow: none; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark text-white border-secondary shadow-lg">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Data Anggota</h4>
                    <a href="anggota.php" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
                <div class="card-body">
                    <form action="proses_edit_anggota.php" method="POST">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label text-secondary">Kode Anggota</label>
                            <input type="text" name="kode_anggota" class="form-control" value="<?= htmlspecialchars($data['kode_anggota']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-secondary">Nama Lengkap</label>
                            <input type="text" name="Nama" class="form-control" value="<?= htmlspecialchars($data['Nama']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($data['no_hp']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary">Alamat Rumah</label>
                            <textarea name="Alamat" class="form-control" rows="3"><?= htmlspecialchars($data['Alamat']) ?></textarea>
                        </div>

                        <hr class="border-secondary">
                        <div class="text-end">
                            <button type="submit" name="update_anggota" class="btn btn-warning fw-bold text-dark"><i class="bi bi-save"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>