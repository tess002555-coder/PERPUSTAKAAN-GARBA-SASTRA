<?php
session_start();
include 'config/koneksi.php';
include 'config/global_theme.php'; // Memuat tema dinamis

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// 1. Logika Pencarian
$keyword = "";
if (isset($_POST['search'])) {
    $keyword = mysqli_real_escape_string($conn, $_POST['keyword']);
}

// 2. Logika Pengurutan (Sorting)
$sort = ""; 
if (isset($_GET['sort']) && $_GET['sort'] != 'none') {
    if ($_GET['sort'] == 'alpha_asc') {
        $sort = "judul_buku ASC, judul ASC";
    } elseif ($_GET['sort'] == 'alpha_desc') {
        $sort = "judul_buku DESC, judul DESC";
    } elseif ($_GET['sort'] == 'stok_desc') {
        $sort = "stok_buku DESC, stok DESC";
    } elseif ($_GET['sort'] == 'terbaru') {
        $sort = "id DESC";
    }
}

// Query dasar dengan filter pencarian jika ada keyword
$query_str = "SELECT * FROM buku WHERE 1=1";
if (!empty($keyword)) {
    $query_str .= " AND (judul_buku LIKE '%$keyword%' OR judul LIKE '%$keyword%' OR penulis_buku LIKE '%$keyword%' OR penulis LIKE '%$keyword%')";
}

if (!empty($sort)) {
    $query_str .= " ORDER BY $sort";
} else {
    $query_str .= " ORDER BY id DESC"; 
}

$query_buku = mysqli_query($conn, $query_str);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - <?= htmlspecialchars($NAMA_PERPUS) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <div class="mb-4">
                <h2 class="<?= $text_judul ?>"><i class="bi bi-book text-aksen-custom"></i> Data Koleksi Buku</h2>
            </div>

            <div class="row g-2 mb-4 d-print-none align-items-center">
                <div class="col-md-5">
                    <form method="POST" action="" class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Cari Judul atau Kategori/Penulis..." value="<?= htmlspecialchars($keyword) ?>">
                        <button class="btn btn-warning" type="submit" name="search"><i class="bi bi-search"></i></button>
                        <?php if (!empty($keyword)): ?>
                            <a href="buku.php" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <div class="col-md-7">
                    <div class="d-flex gap-2 justify-content-md-end align-items-center flex-wrap">
                        <div style="min-width: 160px;">
                            <select class="form-select" onchange="location = this.value;">
                                <option value="buku.php?sort=none" <?= (!isset($_GET['sort']) || $_GET['sort'] == 'none') ? 'selected' : '' ?>>-- Urutkan --</option>
                                <option value="buku.php?sort=terbaru" <?= (isset($_GET['sort']) && $_GET['sort'] == 'terbaru') ? 'selected' : '' ?>>Terbaru</option>
                                <option value="buku.php?sort=alpha_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'alpha_asc') ? 'selected' : '' ?>>Judul (A-Z)</option>
                                <option value="buku.php?sort=alpha_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'alpha_desc') ? 'selected' : '' ?>>Judul (Z-A)</option>
                                <option value="buku.php?sort=stok_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'stok_desc') ? 'selected' : '' ?>>Stok Terbanyak</option>
                            </select>
                        </div>
                        <button onclick="window.print()" class="btn btn-outline-danger fw-bold"><i class="bi bi-file-earmark-pdf"></i> Cetak / Export PDF</button>
                        <a href="tambah_buku.php" class="btn bg-primary-custom fw-bold"><i class="bi bi-plus-lg"></i> Tambah Buku</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php 
                if ($query_buku && mysqli_num_rows($query_buku) > 0):
                    while($row = mysqli_fetch_assoc($query_buku)): 
                        // 1. Ambil nama file dari kolom database yang tersedia
                        $nama_file_gambar = !empty($row['cover_buku']) ? $row['cover_buku'] : (!empty($row['cover']) ? $row['cover'] : '');

                        // 2. Cek apakah file fisik benar-benar ada di dalam folder assets/img/
                        if (!empty($nama_file_gambar) && file_exists("assets/img/" . $nama_file_gambar)) {
                            $src_gambar = "assets/img/" . $nama_file_gambar;
                        } else {
                            // Jika kosong atau file tidak ditemukan di folder, gunakan gambar default placeholder/ikon buku
                            $src_gambar = "https://placehold.co/400x600/e2e8f0/475569?text=No+Cover";
                        }

                        $judul = isset($row['judul_buku']) ? $row['judul_buku'] : (isset($row['judul']) ? $row['judul'] : 'Tanpa Judul');
                        $penulis = isset($row['penulis_buku']) ? $row['penulis_buku'] : (isset($row['penulis']) ? $row['penulis'] : '-');
                        $stok = isset($row['stok_buku']) ? $row['stok_buku'] : (isset($row['stok']) ? $row['stok'] : '0');
                ?>
                <div class="col-md-3 mb-4 col-sm-6 break-print">
                    <div class="card card-custom shadow-sm h-100 border-0">
                        <img src="<?= $src_gambar ?>" class="card-img-top p-2" style="border-radius: 15px; height: 260px; object-fit: cover;" alt="Sampul Buku">
                        <div class="card-body d-flex flex-column justify-content-between pt-1">
                            <div>
                                <h5 class="fw-bold mb-1 text-truncate"><?= htmlspecialchars($judul) ?></h5>
                                <p class="text-muted small mb-2">Penulis: <span class="fw-medium text-aksen-custom"><?= htmlspecialchars($penulis) ?></span></p>
                                <span class="badge bg-secondary mb-3">Stok: <?= $stok ?></span>
                            </div>
                            <div class="d-grid gap-2 d-print-none">
                                <a href="edit_buku.php?id=<?= $row['id'] ?>" class="btn bg-primary-custom btn-sm fw-bold"><i class="bi bi-pencil-square"></i> Edit Buku</a>
                                <a href="hapus_buku.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm fw-bold" onclick="return confirm('Yakin ingin menghapus buku ini?')"><i class="bi bi-trash"></i> Hapus</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else:
                ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-book-half display-1 text-muted"></i>
                    <p class="mt-3 fs-5 <?= $text_judul ?>">Data buku tidak ditemukan.</p>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    .d-print-none, sidebar, .sidebar, button, a.btn, form, select {
        display: none !important;
    }
    body {
        background-color: #fff;
    }
    .col-md-10 {
        width: 100% !important;
    }
    .break-print {
        page-break-inside: avoid;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>