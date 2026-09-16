<?php
session_start();
include 'config/koneksi.php';
include 'config/global_theme.php'; // Ambil tema dinamis

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$pesan = "";

// Proses simpan pengaturan saat tombol ditekan
if (isset($_POST['simpan'])) {
    $nama_perpus = mysqli_real_escape_string($conn, $_POST['nama_perpustakaan']);
    $tema        = mysqli_real_escape_string($conn, $_POST['tema']);
    $warna       = mysqli_real_escape_string($conn, $_POST['warna_aksen']);

    $update = mysqli_query($conn, "UPDATE pengaturan SET nama_perpustakaan='$nama_perpus', tema='$tema', warna_aksen='$warna' WHERE id=1");
    
    if ($update) {
        // Refresh variabel agar perubahan langsung terasa tanpa reload manual
        header("Location: pengaturan.php?status=sukses");
        exit;
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal memperbarui pengaturan: ".mysqli_error($conn)."</div>";
    }
}

if (isset($_get['status']) && $_get['status'] == 'sukses') {
    $pesan = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                <i class='bi bi-check-circle-fill me-2'></i> Pengaturan berhasil disimpan!
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - <?= htmlspecialchars($NAMA_PERPUS) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <h2 class="mb-4"><i class="bi bi-gear-fill text-aksen-custom"></i> Pengaturan Sistem</h2>
            
            <?= $pesan; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card card-custom p-4 shadow-sm">
                        <h5 class="mb-3">Kustomisasi Tampilan Aplikasi</h5>
                        <hr class="border-secondary mb-4">

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Perpustakaan</label>
                                <input type="text" name="nama_perpustakaan" class="form-control" value="<?= htmlspecialchars($NAMA_PERPUS) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mode Tampilan (Tema)</label>
                                <select name="tema" class="form-select">
                                    <option value="dark" <?= ($TEMA_SISTEM == 'dark') ? 'selected' : '' ?>>Mode Gelap (Dark Mode)</option>
                                    <option value="light" <?= ($TEMA_SISTEM == 'light') ? 'selected' : '' ?>>Mode Terang (Light Mode)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Warna Aksen Penanda</label>
                                <select name="warna_aksen" class="form-select">
                                    <option value="primary" <?= ($WARNA_AKSEN == 'primary') ? 'selected' : '' ?>>Biru Default</option>
                                    <option value="warning" <?= ($WARNA_AKSEN == 'warning') ? 'selected' : '' ?>>Kuning Emas</option>
                                    <option value="success" <?= ($WARNA_AKSEN == 'success') ? 'selected' : '' ?>>Hijau Zamrud</option>
                                    <option value="danger" <?= ($WARNA_AKSEN == 'danger') ? 'selected' : '' ?>>Merah Crimson</option>
                                    <option value="info" <?= ($WARNA_AKSEN == 'info') ? 'selected' : '' ?>>Biru Cyan</option>
                                </select>
                            </div>

                            <button type="submit" name="simpan" class="btn bg-primary-custom w-100 fw-bold py-2">
                                <i class="bi bi-save me-2"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>