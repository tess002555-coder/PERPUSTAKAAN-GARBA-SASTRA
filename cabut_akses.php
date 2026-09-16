<?php
session_start();
include 'config/koneksi.php';
include 'catat_log.php';

// Proteksi: Pastikan hanya admin yang login yang bisa mengeksekusi
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Hanya Admin yang berhak mencabut akses khusus.");
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Menggunakan Prepared Statement
    $stmt = $conn->prepare("UPDATE user SET akses_khusus = 0, batas_akses = NULL WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        catatLog($_SESSION['username'], "Mencabut akses khusus dari User ID: " . $id);
    }
    $stmt->close();
}

header("Location: data_user.php");
exit;
?>