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

if (isset($_POST['simpan_anggota'])) {
    // Menyesuaikan name input dengan nama kolom database Anda
    $kode_anggota = mysqli_real_escape_string($conn, trim($_POST['kode_anggota']));
    $nama         = mysqli_real_escape_string($conn, trim($_POST['Nama']));
    $alamat       = mysqli_real_escape_string($conn, trim($_POST['Alamat']));
    $no_hp        = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Cek apakah kode_anggota sudah terdaftar sebelumnya agar tidak duplikat
    $cek_kode = mysqli_query($conn, "SELECT id FROM anggota WHERE kode_anggota = '$kode_anggota'");
    if(mysqli_num_rows($cek_kode) > 0) {
        echo "<script>alert('Eror: Kode Anggota sudah terdaftar!'); window.location='anggota.php';</script>";
        exit;
    }

    // Urutan kolom disesuaikan: kode_anggota, Nama, Alamat, no_hp, email
    $stmt = $conn->prepare("INSERT INTO anggota (kode_anggota, Nama, Alamat, no_hp, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $kode_anggota, $nama, $alamat, $no_hp, $email);

    if ($stmt->execute()) {
        if(function_exists('catatLog')){
            catatLog($_SESSION['username'], "Menambahkan anggota baru: " . $nama);
        }
        header("Location: anggota.php");
        exit;
    } else {
        echo "Gagal menyimpan data: " . $conn->error;
    }
    $stmt->close();
} else {
    header("Location: anggota.php");
    exit;
}
?>