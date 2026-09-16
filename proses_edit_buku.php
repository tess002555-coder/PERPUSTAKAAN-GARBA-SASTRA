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

if (isset($_POST['update'])) {
    $id         = (int)$_POST['id'];
    $judul      = mysqli_real_escape_string($conn, $_POST['Judul']);
    $penulis    = mysqli_real_escape_string($conn, $_POST['Penulis']);
    $penerbit   = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $kategori   = mysqli_real_escape_string($conn, $_POST['Kategori']);
    $tahun      = (int)$_POST['tahun'];
    $stok       = (int)$_POST['Stok'];
    $cover_lama = $_POST['cover_lama'];

    // Defaultkan nama cover dengan cover lama
    $nama_cover = $cover_lama;

    // Cek apakah user mengunggah file cover baru
    if (isset($_FILES['Cover']['name']) && $_FILES['Cover']['name'] != "") {
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        $x = explode('.', $_FILES['Cover']['name']);
        $ekstensi = strtolower(end($x));
        $file_tmp = $_FILES['Cover']['tmp_name'];
        
        // Buat nama unik untuk cover baru
        $nama_cover_baru = time() . '_' . $_FILES['Cover']['name'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            // Upload file baru
            if(move_uploaded_file($file_tmp, 'cover/' . $nama_cover_baru)){
                $nama_cover = $nama_cover_baru;
                
                // Hapus cover lama dari folder (jika bukan default.png) agar tidak menumpuk
                if ($cover_lama != "default.png" && file_exists('cover/' . $cover_lama)) {
                    unlink('cover/' . $cover_lama);
                }
            }
        } else {
            echo "<script>alert('Ekstensi gambar salah! Hanya jpg, jpeg, png.'); window.location='edit_buku.php?id=$id';</script>";
            exit;
        }
    }

    // Update data ke database menggunakan Prepared Statement
    $stmt = $conn->prepare("UPDATE buku SET Judul=?, Penulis=?, penerbit=?, Kategori=?, tahun=?, Stok=?, Cover=? WHERE id=?");
    $stmt->bind_param("ssssiisi", $judul, $penulis, $penerbit, $kategori, $tahun, $stok, $nama_cover, $id);

    if ($stmt->execute()) {
        if(function_exists('catatLog')){
            catatLog($_SESSION['username'], "Mengedit buku: " . $judul);
        }
        echo "<script>alert('Data buku berhasil diperbarui!'); window.location='buku.php';</script>";
    } else {
        echo "Gagal mengupdate data: " . $conn->error;
    }
    
    $stmt->close();
} else {
    header("Location: buku.php");
}
exit;
?>