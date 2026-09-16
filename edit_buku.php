<?php
session_start();
include 'config/koneksi.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data buku berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM buku WHERE id = '$id'");
if(mysqli_num_rows($query) == 0){
    echo "<script>alert('Data buku tidak ditemukan!'); window.location='buku.php';</script>";
    exit;
}
$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background:#111827; }
        .form-control { background-color: #1f2937; color: white; border: 1px solid #374151; }
        .form-control:focus { background-color: #374151; color: white; border-color: #3b82f6; box-shadow: none; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-white border-secondary shadow-lg">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Data Buku</h4>
                    <a href="buku.php" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
                <div class="card-body">
                    <form action="proses_edit_buku.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        <input type="hidden" name="cover_lama" value="<?= $data['Cover'] ?>">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label text-secondary">Judul Buku</label>
                                    <input type="text" name="Judul" class="form-control" value="<?= htmlspecialchars($data['Judul']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-secondary">Penulis</label>
                                    <input type="text" name="Penulis" class="form-control" value="<?= htmlspecialchars($data['Penulis']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-secondary">Penerbit</label>
                                    <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($data['penerbit']) ?>" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label text-secondary">Kategori</label>
                                        <input type="text" name="Kategori" class="form-control" value="<?= htmlspecialchars($data['Kategori']) ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label text-secondary">Tahun Terbit</label>
                                        <input type="number" name="tahun" class="form-control" value="<?= htmlspecialchars($data['tahun']) ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label text-secondary">Stok</label>
                                        <input type="number" name="Stok" class="form-control" value="<?= htmlspecialchars($data['Stok']) ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 text-center">
                                <label class="form-label text-secondary">Cover Saat Ini</label><br>
                                <img src="cover/<?= $data['Cover'] ? $data['Cover'] : 'default.png' ?>" class="img-thumbnail bg-secondary border-0 mb-3" style="max-height: 200px;" alt="Cover Lama">
                                
                                <div class="mb-3 text-start">
                                    <label class="form-label text-secondary small">Ganti Cover (Opsional)</label>
                                    <input type="file" name="Cover" class="form-control form-control-sm">
                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti cover.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="border-secondary">
                        <div class="text-end">
                            <button type="submit" name="update" class="btn btn-warning fw-bold text-dark"><i class="bi bi-save"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>