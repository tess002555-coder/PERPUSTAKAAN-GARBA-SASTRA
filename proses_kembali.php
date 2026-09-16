<?php
session_start();
include 'config/koneksi.php';
include 'catat_log.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

if(isset($_GET['id'])){
    $id = (int)$GET['id'];

    // Ambil detail data peminjaman
    $query = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id = $id");
    $row = mysqli_fetch_assoc($query);

    if($row && $row['status'] == 'Dipinjam') {
        $judul_buku = $row['Judul'];
        
        // Hitung denda final akhir
        $tgl_kembali = strtotime($row['tanggal_kembali']);
        $tgl_sekarang = strtotime(date('Y-m-d'));
        $denda_akhir = 0;

        if($tgl_sekarang > $tgl_kembali) {
            $selisih = ($tgl_sekarang - $tgl_kembali) / (60 * 60 * 24);
            $denda_akhir = $selisih * 2000;
        }

        mysqli_begin_transaction($conn);

        // 1. Update status peminjaman dan denda
        $stmt_up = $conn->prepare("UPDATE peminjaman SET status = 'Kembali', denda = ? WHERE id = ?");
        $stmt_up->bind_param("ii", $denda_akhir, $id);
        $res1 = $stmt_up->execute();

        // 2. Kembalikan stok buku ke jumlah semula (+1)
        $stmt_buku = $conn->prepare("UPDATE buku SET Stok = Stok + 1 WHERE Judul = ?");
        $stmt_buku->bind_param("s", $judul_buku);
        $res2 = $stmt_buku->execute();

        if($res1 && $res2) {
            mysqli_commit($conn);
            catatLog($_SESSION['username'], "Memproses pengembalian buku: " . $judul_buku . " (Denda: Rp " . $denda_akhir . ")");
            header("Location: peminjaman.php");
            exit;
        } else {
            mysqli_rollback($conn);
            echo "Gagal memproses pengembalian.";
        }
    }
}

header("Location: peminjaman.php");
exit;
?>