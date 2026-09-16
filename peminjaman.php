<?php
session_start();
include 'config/koneksi.php';
include 'config/global_theme.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Menggunakan query yang hanya memanggil kolom yang pasti ada
$query_peminjaman = mysqli_query($conn, "SELECT p.*, 
                                          a.Nama, 
                                          b.judul
                                          FROM peminjaman p 
                                          JOIN anggota a ON p.id_anggota = a.id 
                                          JOIN buku b ON p.id_buku = b.id 
                                          ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peminjaman - <?= htmlspecialchars($NAMA_PERPUS) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-journal-arrow-up"></i> Transaksi Peminjaman Buku</h2>
                <a href="tambah_peminjaman.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Pinjam Buku Baru</a>
            </div>

            <div class="card p-4 shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Anggota</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($query_peminjaman && mysqli_num_rows($query_peminjaman) > 0):
                                $no = 1;
                                while($row = mysqli_fetch_assoc($query_peminjaman)): 
                                    // Menggunakan kolom yang sesuai database Anda: Nama dan judul
                                    $nama_anggota = $row['Nama']; 
                                    $judul_buku   = $row['judul'];

                                    // Pastikan nama kolom tanggal sesuai dengan database Anda
                                    // Jika error, coba ganti 'tgl_pinjam' dengan 'tanggal_pinjam'
                                    $tgl_p = $row['tgl_pinjam'] ?? '-';
                                    $tgl_k = $row['tgl_kembali'] ?? '-';

                                    $status = $row['status'];
                                    $badge_status = ($status == 'dipinjam') ? 'bg-warning text-dark' : 'bg-success';
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($nama_anggota) ?></td>
                                <td><?= htmlspecialchars($judul_buku) ?></td>
                                <td><?= $tgl_p ?></td>
                                <td><?= $tgl_k ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $badge_status ?>"><?= $status ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($status == 'dipinjam'): ?>
                                        <a href="kembalikan_buku.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Kembalikan buku?')">Kembalikan</a>
                                    <?php else: ?>
                                        <span class="text-muted">Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada transaksi.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>