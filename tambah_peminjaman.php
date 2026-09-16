<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['username'])) { header("Location: login.php"); exit; }

// Proses simpan data
if (isset($_POST['simpan'])) {
    $id_anggota = $_POST['id_anggota'];
    $id_buku    = $_POST['id_buku'];
    $tgl_pinjam = date('Y-m-d'); // Tanggal otomatis hari ini
    $status     = 'dipinjam';

    $query = "INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, status) 
              VALUES ('$id_anggota', '$id_buku', '$tgl_pinjam', '$status')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Peminjaman berhasil dicatat!'); window.location='peminjaman.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 col-md-6 mx-auto">
        <h4 class="mb-4">Form Peminjaman Buku</h4>
        <form method="POST">
            <div class="mb-3">
                <label>Pilih Anggota</label>
                <select name="id_anggota" class="form-select" required>
                    <?php 
                    $anggota = mysqli_query($conn, "SELECT * FROM anggota");
                    while($a = mysqli_fetch_assoc($anggota)): ?>
                        <option value="<?= $a['id'] ?>"><?= $a['Nama'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Pilih Buku</label>
                <select name="id_buku" class="form-select" required>
                    <?php 
                    $buku = mysqli_query($conn, "SELECT * FROM buku");
                    while($b = mysqli_fetch_assoc($buku)): ?>
                        <option value="<?= $b['id'] ?>"><?= $b['judul'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="simpan" class="btn btn-primary w-100">Simpan Peminjaman</button>
            <a href="peminjaman.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
        </form>
    </div>
</div>
</body>
</html>