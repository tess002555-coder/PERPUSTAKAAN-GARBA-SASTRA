<?php
session_start();
include 'config/koneksi.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM buku ORDER BY Judul ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Buku Perpustakaan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #444; }
        th { background-color: #f2f2f2; padding: 10px; text-align: left; }
        td { padding: 8px; }
        .footer-date { text-align: right; margin-top: 40px; font-size: 0.9em; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN DATA BUKU</h2>
        <h3>E-PERPUSTAKAAN DIGITAL</h3>
        <p>Jl. Raya Perpustakaan No. 22</p>
        <hr style="border: 1px solid #000;">
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Kategori</th>
                <th style="width: 10%;">Tahun</th>
                <th style="width: 10%;">Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($row = mysqli_fetch_assoc($query)){ 
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><strong><?= htmlspecialchars($row['Judul']); ?></strong></td>
                    <td><?= htmlspecialchars($row['Penulis']); ?></td>
                    <td><?= htmlspecialchars($row['penerbit']); ?></td>
                    <td><?= htmlspecialchars($row['Kategori']); ?></td>
                    <td><?= $row['tahun']; ?></td>
                    <td><?= $row['Stok']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="footer-date">
        Dicetak oleh: <strong><?= htmlspecialchars($_SESSION['username']); ?></strong><br>
        Tanggal: <?= date('d F Y H:i'); ?>
    </div>

    <script>
        window.print();
    </script>

</body>
</html>