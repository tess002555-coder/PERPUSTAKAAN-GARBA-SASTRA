<?php
session_start();
include 'config/koneksi.php';
include 'config/global_theme.php'; // <-- Memuat tema dinamis dari database

// Pastikan user sudah login sebelum mengakses halaman ini
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// 1. Logika Query Penghitung Data Dashboard
$total_buku     = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM buku"));
$total_anggota  = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM anggota"));
$total_dipinjam = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjaman WHERE status='dipinjam'"));
$total_telat    = 0; // Silakan sesuaikan logika denda/keterlambatan sistem Anda di sini

// 2. Query untuk Mengambil Data Grafik Buku Paling Sering Dipinjam
$labels = [];
$counts = [];
$query_grafik = mysqli_query($conn, "SELECT b.judul, COUNT(p.id_buku) as jumlah 
                                     FROM peminjaman p 
                                     JOIN buku b ON p.id_buku = b.id 
                                     GROUP BY p.id_buku 
                                     ORDER BY jumlah DESC LIMIT 5");

if ($query_grafik && mysqli_num_rows($query_grafik) > 0) {
    while ($row = mysqli_fetch_assoc($query_grafik)) {
        $labels[] = $row['judul'];
        $counts[] = (int)$row['jumlah'];
    }
} else {
    // Fallback data jika belum ada transaksi peminjaman di database
    $labels = ['Belum Ada Data', 'Belum Ada Data'];
    $counts = [0, 0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= htmlspecialchars($NAMA_PERPUS) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body> 

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-speedometer2 text-aksen-custom"></i> Dashboard Perpustakaan</h2>
                <span class="badge bg-secondary p-2">Sesi: <?= htmlspecialchars($_SESSION['username']); ?> (<?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Petugas'; ?>)</span>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white p-3 shadow-sm border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Buku</h6>
                                <h3 class="fw-bold mb-0"><?= $total_buku; ?></h3>
                            </div>
                            <i class="bi bi-book fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white p-3 shadow-sm border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Anggota</h6>
                                <h3 class="fw-bold mb-0"><?= $total_anggota; ?></h3>
                            </div>
                            <i class="bi bi-people fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark p-3 shadow-sm border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-dark-50 mb-1">Sedang Dipinjam</h6>
                                <h3 class="fw-bold mb-0"><?= $total_dipinjam; ?></h3>
                            </div>
                            <i class="bi bi-journal-arrow-up fs-1 text-dark-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white p-3 shadow-sm border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Keterlambatan</h6>
                                <h3 class="fw-bold mb-0"><?= $total_telat; ?></h3>
                            </div>
                            <i class="bi bi-exclamation-triangle fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card card-custom p-4 shadow-sm">
                        <h5><i class="bi bi-bar-chart-line text-warning"></i> Buku Paling Sering Dipinjam</h5>
                        <hr class="border-secondary">
                        <div style="height: 300px; position: relative;">
                            <canvas id="chartBukuTerlaris"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card card-custom p-4 shadow-sm h-100">
                        <h5><i class="bi bi-clock-history text-info"></i> Log Singkat Aktivitas</h5>
                        <hr class="border-secondary">
                        <div class="table-responsive">
                            <table class="table <?= $css_table ?> table-sm table-borderless small">
                                <tbody>
                                    <?php
                                    $log_query = mysqli_query($conn, "SELECT username, aktivitas FROM log_aktivitas ORDER BY id DESC LIMIT 5");
                                    if($log_query && mysqli_num_rows($log_query) > 0) {
                                        while($l = mysqli_fetch_assoc($log_query)) {
                                            echo "<tr>";
                                            // Warna nama user log mengikuti aksen dinamis pilihan Admin
                                            echo "<td class='fw-bold text-aksen-custom'>".htmlspecialchars($l['username'])."</td>";
                                            echo "<td>".htmlspecialchars($l['aktivitas'])."</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='2' class='text-center py-3 " . ($text_muted ?? 'text-secondary') . "'>Belum ada aktivitas baru.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Logika Render Grafik Dinamis Menggunakan Chart.js
const ctx = document.getElementById('chartBukuTerlaris').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels); ?>,
        datasets: [{
            label: 'Jumlah Kali Dipinjam',
            data: <?= json_encode($counts); ?>,
            backgroundColor: 'rgba(245, 158, 11, 0.7)', // Transparansi aksen kuning
            borderColor: 'rgba(245, 158, 11, 1)',
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                ticks: { color: 'inherit' },
                grid: { color: 'rgba(128, 128, 128, 0.15)' }
            },
            x: { 
                ticks: { color: 'inherit' },
                grid: { display: false }
            }
        }
    }
});
</script>
</body>
</html>