<?php
$current_page = basename($_SERVER['PHP_SELF']);
// Pastikan variabel role sudah ada di session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<div class="col-md-2 min-vh-100 p-3 shadow" style="<?= $bg_sidebar ?>">
    <div class="text-center my-4">
        <i class="bi bi-bank fs-1 text-warning"></i>
        <h5 class="mt-2 text-uppercase fw-bold text-wrap px-2 <?= $text_sidebar ?>"><?= htmlspecialchars($NAMA_PERPUS) ?></h5>
        <hr class="border-secondary">
    </div>

    <div class="nav flex-column nav-pills">
        <a href="dashboard.php" class="nav-link mb-2 p-3 d-flex align-items-center <?= $text_sidebar ?> <?= ($current_page == 'dashboard.php') ? 'bg-primary-custom fw-bold' : 'hover-effect' ?>">
            <i class="bi bi-speedometer2 me-3 fs-5"></i> Dashboard
        </a>

        <a href="peminjaman.php" class="nav-link mb-2 p-3 d-flex align-items-center <?= $text_sidebar ?> <?= ($current_page == 'peminjaman.php') ? 'bg-primary-custom fw-bold' : 'hover-effect' ?>">
            <i class="bi bi-journal-arrow-up me-3 fs-5"></i> Peminjaman
        </a>

        <?php if ($role === 'admin' || $role === 'petugas'): ?>
            <hr class="border-secondary my-2">
            <div class="px-3 text-muted small fw-bold text-uppercase mb-2">Admin Panel</div>
            
            <a href="buku.php" class="nav-link mb-2 p-3 d-flex align-items-center <?= $text_sidebar ?> <?= ($current_page == 'buku.php') ? 'bg-primary-custom fw-bold' : 'hover-effect' ?>">
                <i class="bi bi-book me-3 fs-5"></i> Data Buku
            </a>
            
            <a href="anggota.php" class="nav-link mb-2 p-3 d-flex align-items-center <?= $text_sidebar ?> <?= ($current_page == 'anggota.php') ? 'bg-primary-custom fw-bold' : 'hover-effect' ?>">
                <i class="bi bi-people me-3 fs-5"></i> Data Anggota
            </a>
            
            <a href="pengaturan.php" class="nav-link mb-2 p-3 d-flex align-items-center <?= $text_sidebar ?> <?= ($current_page == 'pengaturan.php') ? 'bg-primary-custom fw-bold' : 'hover-effect' ?>">
                <i class="bi bi-gear me-3 fs-5 text-warning"></i> Pengaturan
            </a>
        <?php endif; ?>

        <hr class="border-secondary my-4">
        <a href="logout.php" class="nav-link text-danger p-3 d-flex align-items-center fw-bold" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
            <i class="bi bi-box-arrow-right me-3 fs-5"></i> Keluar
        </a>
    </div>
</div>

<style>
.hover-effect:hover {
    background: rgba(128, 128, 128, 0.15);
    color: var(--bs-<?= $WARNA_AKSEN ?>) !important;
}
.bg-primary-custom { background-color: var(--bs-<?= $WARNA_AKSEN ?>); color: white !important; }
</style>