<?php
session_start();
include 'config/koneksi.php';
include 'config/global_theme.php'; // Memuat tema dinamis
include 'config/check_role.php';   // Tambahkan ini untuk fungsi is_admin()

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// 1. Logika Pencarian Anggota
$keyword = "";
if (isset($_POST['search'])) {
    $keyword = mysqli_real_escape_string($conn, $_POST['keyword']);
}

// Query dasar mengambil data anggota
$query_str = "SELECT * FROM anggota WHERE 1=1";
if (!empty($keyword)) {
    $query_str .= " AND (nama_lengkap LIKE '%$keyword%' OR Nama LIKE '%$keyword%' OR nama LIKE '%$keyword%' OR kode_anggota LIKE '%$keyword%')";
}
$query_str .= " ORDER BY id DESC";

$query_anggota = mysqli_query($conn, $query_str);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota - <?= htmlspecialchars($NAMA_PERPUS) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .form-control-dark::placeholder { color: rgba(255, 255, 255, 0.6) !important; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2 class="<?= $text_judul ?>"><i class="bi bi-people text-aksen-custom"></i> Manajemen Anggota</h2>
            </div>

            <div class="row g-2 mb-4 d-print-none align-items-center">
                <div class="col-md-5">
                    <form method="POST" action="" class="input-group">
                        <input type="text" name="keyword" class="form-control bg-dark text-white border-secondary form-control-dark" placeholder="Cari Nama atau Kode Anggota..." value="<?= htmlspecialchars($keyword) ?>">
                        <button class="btn btn-warning" type="submit" name="search"><i class="bi bi-search"></i></button>
                        <?php if (!empty($keyword)): ?>
                            <a href="anggota.php" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <div class="col-md-7">
                    <div class="d-flex gap-2 justify-content-md-end align-items-center flex-wrap">
                        <button onclick="window.print()" class="btn btn-outline-danger fw-bold"><i class="bi bi-file-earmark-pdf"></i> Cetak / Export PDF</button>
                        <a href="tambah_anggota.php" class="btn bg-primary-custom fw-bold"><i class="bi bi-person-plus"></i> Tambah Anggota</a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm card-custom">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-white">
                            <thead class="table-dark">
                                <tr>
                                    <th width="60" class="text-center">No</th>
                                    <th>Kode Anggota</th>
                                    <th>Nama Lengkap</th>
                                    <th>No. HP</th>
                                    <th>Email</th>
                                    <th width="150" class="text-center d-print-none">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if ($query_anggota && mysqli_num_rows($query_anggota) > 0):
                                    while($row = mysqli_fetch_assoc($query_anggota)): 
                                        $kode_agt = isset($row['kode_anggota']) ? $row['kode_anggota'] : (isset($row['Kode']) ? $row['Kode'] : '-');
                                        $nama_agt = (isset($row['Nama'])) ? $row['Nama'] : ((isset($row['nama_lengkap'])) ? $row['nama_lengkap'] : ((isset($row['nama'])) ? $row['nama'] : 'Tanpa Nama'));
                                        $no_hp = isset($row['no_hp']) ? $row['no_hp'] : (isset($row['No_HP']) ? $row['No_HP'] : (isset($row['hp']) ? $row['hp'] : '-'));
                                        $email = isset($row['email']) ? $row['email'] : (isset($row['Email']) ? $row['Email'] : '-');
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><span class="badge bg-secondary py-2 px-3"><?= htmlspecialchars($kode_agt) ?></span></td>
                                    <td class="fw-bold text-warning"><?= htmlspecialchars($nama_agt) ?></td>
                                    <td><?= htmlspecialchars($no_hp) ?></td>
                                    <td><?= htmlspecialchars($email) ?></td>
                                    <td class="text-center d-print-none">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="edit_anggota.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                            
                                            <?php if (is_admin()): ?>
                                                <a href="hapus_anggota.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus anggota ini?')" title="Hapus"><i class="bi bi-trash"></i></a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-people display-4"></i>
                                        <p class="mt-2 mb-0">Tidak ada data anggota ditemukan.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>