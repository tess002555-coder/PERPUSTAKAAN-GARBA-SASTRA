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

if (isset($_POST['update_anggota'])) {
    $id            = (int)$_POST['id'];
    $kode_anggota  = mysqli_real_escape_string($conn, trim($_POST['kode_anggota']));
    $nama          = mysqli_real_escape_string($conn, trim($_POST['Nama']));
    $alamat        = mysqli_real_escape_string($conn, trim($_POST['Alamat']));
    $no_hp         = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    $email         = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Jalankan query update menggunakan Prepared Statement
    $stmt = $conn->prepare("UPDATE anggota SET kode_anggota=?, Nama=?, Alamat=?, no_hp=?, email=? WHERE id=?");
    $stmt->bind_param("sssssi", $kode_anggota, $nama, $alamat, $no_hp, $email, $id);

    if ($stmt->execute()) {
        if(function_exists('catatLog')){
            catatLog($_SESSION['username'], "Mengubah data anggota: " . $nama);
        }
        echo "<script>alert('Data anggota berhasil diperbarui!'); window.location='anggota.php';</script>";
    } else {
        echo "Gagal mengupdate data: " . $conn->error;
    }
    
    $stmt->close();
} else {
    header("Location: anggota.php");
}
exit;
?>