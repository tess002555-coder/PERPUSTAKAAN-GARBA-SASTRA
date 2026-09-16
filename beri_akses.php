<?php
session_start();
include 'config/koneksi.php';
include 'catat_log.php';

// Proteksi: Pastikan hanya admin yang login yang bisa mengeksekusi
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Hanya Admin yang berhak memberikan akses khusus.");
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Mengamankan input ID agar harus berupa angka

    // Menggunakan Prepared Statement untuk keamanan SQL Injection
    $stmt = $conn->prepare("UPDATE user SET akses_khusus = 1, batas_akses = DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        catatLog($_SESSION['username'], "Memberikan akses khusus ke User ID: " . $id);
    }
    $stmt->close();
}

header("Location: data_user.php");
exit;
?>