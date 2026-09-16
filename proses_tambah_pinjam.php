<?php
session_start();
include 'config/koneksi.php';
include 'config/cek_hak_akses.php'; // <-- WAJIB disisipkan di sini

// Kode proses database bawaan Anda di bawah...
// $query = mysqli_query($conn, "INSERT INTO buku ...");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['pinjam_buku'])) {
    $id_anggota      = (int)$_POST['id_anggota'];
    $id_buku         = (int)$_POST['id_buku'];
    $tanggal_pinjam  = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $status          = "Pinjam";
    $denda           = 0;

    mysqli_begin_transaction($conn);

    try {
        // 1. Ambil data Nama Anggota untuk history snapshot di tabel peminjaman
        $q_anggota = mysqli_query($conn, "SELECT Nama FROM anggota WHERE id = '$id_anggota'");
        $d_anggota = mysqli_fetch_assoc($q_anggota);
        $nama_mhs  = $d_anggota['Nama'];

        // 2. Ambil data Judul Buku & cek stok terakhir
        $cek_buku = mysqli_query($conn, "SELECT Judul, Stok FROM buku WHERE id = '$id_buku' FOR UPDATE");
        $data_buku = mysqli_fetch_assoc($cek_buku);
        $judul_buku = $data_buku['Judul'];
        
        if ($data_buku['Stok'] <= 0) {
            throw new Exception("Stok buku yang dipilih sedang habis!");
        }

        // 3. Insert ke tabel peminjaman sesuai struktur kolom gambar Anda
        $stmt_pinjam = $conn->prepare("INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status, denda, Nama, Judul) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_pinjam->bind_param("iisssiss", $id_anggota, $id_buku, $tanggal_pinjam, $tanggal_kembali, $status, $denda, $nama_mhs, $judul_buku);
        $stmt_pinjam->execute();

        // 4. Potong stok buku otomatis
        $stmt_update_stok = $conn->prepare("UPDATE buku SET Stok = Stok - 1 WHERE id = ?");
        $stmt_update_stok->bind_param("i", $id_buku);
        $stmt_update_stok->execute();

        mysqli_commit($conn);

        if (function_exists('catatLog')) {
            catatLog($_SESSION['username'], "Memproses pinjaman buku: " . $judul_buku . " oleh " . $nama_mhs);
        }

        echo "<script>alert('Peminjaman buku berhasil diproses!'); window.location='peminjaman.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal memproses peminjaman: " . $e->getMessage() . "'); window.location='peminjaman.php';</script>";
    }

    $stmt_pinjam->close();
    $stmt_update_stok->close();
} else {
    header("Location: peminjaman.php");
}
exit;
?>