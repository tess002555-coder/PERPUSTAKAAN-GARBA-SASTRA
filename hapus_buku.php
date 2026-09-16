<?php
session_start();
include 'config/koneksi.php';
include 'config/cek_hak_akses.php'; // <-- WAJIB disisipkan di sini
include 'config/check_role.php';

// Kode proses database bawaan Anda di bawah...
// $query = mysqli_query($conn, "INSERT INTO buku ...");

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 1. Ambil data buku terlebih dahulu untuk mengecek nama file cover-nya
    $query_cek = mysqli_query($conn, "SELECT Judul, Cover FROM buku WHERE id = '$id'");
    
    if (mysqli_num_rows($query_cek) > 0) {
        $data = mysqli_fetch_assoc($query_cek);
        $judul_buku = $data['Judul'];
        $nama_cover = $data['Cover'];

        // 2. Hapus file cover fisik dari folder jika bukan default.png
        if ($nama_cover != "default.png" && $nama_cover != "") {
            $path_cover = "cover/" . $nama_cover;
            if (file_exists($path_cover)) {
                unlink($path_cover); // Perintah PHP untuk menghapus file
            }
        }

        // 3. Hapus data dari database menggunakan Prepared Statement
        $stmt = $conn->prepare("DELETE FROM buku WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Catat ke log aktivitas (opsional, jika fitur log aktif)
            if (function_exists('catatLog')) {
                catatLog($_SESSION['username'], "Menghapus buku: " . $judul_buku);
            }
            
            echo "<script>
                    alert('Data buku berhasil dihapus!');
                    window.location.href = 'buku.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menghapus data dari database!');
                    window.location.href = 'buku.php';
                  </script>";
        }
        $stmt->close();
    } else {
        echo "<script>
                alert('Data buku tidak ditemukan!');
                window.location.href = 'buku.php';
              </script>";
    }
} else {
    header("Location: buku.php");
}
exit;
?>