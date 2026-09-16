<?php
session_start();
include 'config/koneksi.php';
include 'config/cek_hak_akses.php'; // <-- WAJIB disisipkan di sini
include 'config/check_role.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (!is_admin()) {
// Cek apakah ada ID yang dikirim melalui URL
    if (isset($_GET['id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        
        // KUNCI: Harus ada WHERE id = '$id' agar hanya 1 data yang dihapus
        $query = "DELETE FROM anggota WHERE id = '$id'";
        
        if (mysqli_query($conn, $query)) {
            // Berhasil dihapus
            echo "<script>alert('Anggota berhasil dihapus!'); window.location='anggota.php';</script>";
        } else {
            // Gagal dihapus
            echo "<script>alert('Gagal menghapus data: " . mysqli_error($conn) . "'); window.location='anggota.php';</script>";
        }
    } else {
        // Jika tidak ada ID, kembali ke halaman anggota
        header("Location: anggota.php");
         }
}
?>