<?php
session_start();
include 'config/koneksi.php';
include 'catat_log.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM user WHERE Username='".$_SESSION['username']."'");
$user = mysqli_fetch_assoc($query);

if($user['Role']!='admin' && ($user['akses_khusus']!=1 || strtotime($user['batas_akses']) < time())){
    die("Akses ditolak oleh admin!");
}

if(isset($_GET['id'])){
    $id = (int)$_GET['id'];

    // Cari tahu data apa yang dihapus untuk dicatat di log
    $res_pem = mysqli_query($conn, "SELECT Nama, Judul FROM peminjaman WHERE id='$id'");
    $data_pem = mysqli_fetch_assoc($res_pem);

    if($data_pem){
        $stmt = $conn->prepare("DELETE FROM peminjaman WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            catatLog($_SESSION['username'], "Menghapus riwayat peminjaman buku: " . $data_pem['Judul'] . " oleh " . $data_pem['Nama']);
        }
        $stmt->close();
    }
}

header("Location: peminjaman.php");
exit;
?>