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

if (isset($_POST['simpan'])) {
    $judul    = mysqli_real_escape_string($conn, $_POST['Judul']);
    $penulis  = mysqli_real_escape_string($conn, $_POST['Penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $kategori = mysqli_real_escape_string($conn, $_POST['Kategori']);
    $tahun    = (int)$_POST['tahun'];
    $stok     = (int)$_POST['Stok'];
    
    // Menerima input hidden dari form yang berisi URL gambar dari API
    $api_url  = $_POST['api_cover_url'] ?? '';

    // Default jika admin tidak upload file cover secara manual
    $nama_cover = "default.png";

    // KONDISI 1: Memproses upload file jika ada file yang dipilih manual
    if (isset($_FILES['Cover']['name']) && $_FILES['Cover']['name'] != "") {
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        $x = explode('.', $_FILES['Cover']['name']);
        $ekstensi = strtolower(end($x));
        $file_tmp = $_FILES['Cover']['tmp_name'];
        
        $nama_cover = time() . '_' . $_FILES['Cover']['name'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            move_uploaded_file($file_tmp, 'cover/' . $nama_cover);
        } else {
            echo "<script>alert('Ekstensi gambar yang diperbolehkan hanya jpg, jpeg, atau png!'); window.location='buku.php';</script>";
            exit;
        }
    } 
    // KONDISI 2: Auto-Download jika upload manual kosong tapi ada link dari API
    elseif (!empty($api_url)) {
        $nama_cover = "auto_" . time() . ".jpg";
        $path_simpan = 'cover/' . $nama_cover;

        // Gunakan cURL untuk mengunduh gambar ke folder server
        $ch = curl_init($api_url);
        $fp = fopen($path_simpan, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $sukses = curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        // Jika download gagal (file 0 byte), balikkan ke default
        if (!$sukses || filesize($path_simpan) == 0) {
            @unlink($path_simpan);
            $nama_cover = "default.png";
        }
    }

    // Menggunakan Prepared Statement demi keamanan database
    $stmt = $conn->prepare("INSERT INTO buku (Judul, Penulis, penerbit, Kategori, tahun, Stok, Cover) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiis", $judul, $penulis, $penerbit, $kategori, $tahun, $stok, $nama_cover);

    if ($stmt->execute()) {
        if(function_exists('catatLog')){
            catatLog($_SESSION['username'], "Menambahkan data buku baru (Auto/Manual): " . $judul);
        }
        header("Location: buku.php");
        exit;
    } else {
        echo "Gagal menyimpan data buku: " . $conn->error;
    }
    $stmt->close();
} else {
    header("Location: buku.php");
    exit;
}
?>