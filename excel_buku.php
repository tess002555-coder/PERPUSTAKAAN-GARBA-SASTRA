<?php
session_start();
include 'config/koneksi.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

// Memaksa browser mengunduh file dengan format Excel .xls
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Data_Buku_" . date('Ymd') . ".xls");

$query = mysqli_query($conn, "SELECT * FROM buku ORDER BY Judul ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Export Excel Data Buku</title>
</head>
<body>

    <center>
        <h2>REKAPITULASI DATA BUKU PERPUSTAKAAN</h2>
    </center>

    <table border="1">
        <thead>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <th>No</th>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Kategori</th>
                <th>Tahun Terbit</th>
                <th>Jumlah Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($row = mysqli_fetch_assoc($query)){ 
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['Judul']); ?></td>
                    <td><?= htmlspecialchars($row['Penulis']); ?></td>
                    <td><?= htmlspecialchars($row['penerbit']); ?></td>
                    <td><?= htmlspecialchars($row['Kategori']); ?></td>
                    <td><?= $row['tahun']; ?></td>
                    <td><?= $row['Stok']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>