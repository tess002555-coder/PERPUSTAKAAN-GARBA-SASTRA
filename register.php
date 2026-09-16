<?php
session_start();
include 'config/koneksi.php'; // Sesuaikan lokasi file koneksi Anda

$pesan_status = "";
$tahap_otp = false;

// 1. PROSES PENDAFTARAN AWAL
if (isset($_POST['daftar'])) {
    $nama         = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp        = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $email        = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat       = mysqli_real_escape_string($conn, $_POST['alamat']);
    $username     = mysqli_real_escape_string($conn, $_POST['username']);
    $password     = md5($_POST['password']); // Enkripsi MD5
    
    $kode_anggota = "AGT-" . date('Y') . "-" . rand(100, 999);
    $otp          = rand(100000, 999999); // Generate 6 digit OTP

    $query = "INSERT INTO anggota (kode_anggota, Nama, Alamat, no_hp, email, username, password, otp, status_aktivasi) 
              VALUES ('$kode_anggota', '$nama', '$alamat', '$no_hp', '$email', '$username', '$password', '$otp', 'belum_aktivasi')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['id_pendaftar'] = $kode_anggota;
        $tahap_otp = true;
        $pesan_status = "<div class='alert alert-success'>Registrasi berhasil! Mohon catat kode OTP Anda: <b>$otp</b></div>";
    } else {
        $pesan_status = "<div class='alert alert-danger'>Gagal mendaftar: " . mysqli_error($conn) . "</div>";
    }
}

// 2. PROSES VERIFIKASI OTP
if (isset($_POST['verifikasi'])) {
    $id_pendaftar = $_SESSION['id_pendaftar'];
    $input_otp    = mysqli_real_escape_string($conn, $_POST['otp_input']);

    $cek = mysqli_query($conn, "SELECT * FROM anggota WHERE kode_anggota='$id_pendaftar' AND otp='$input_otp'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE anggota SET status_aktivasi='aktif', otp='' WHERE kode_anggota='$id_pendaftar'");
        echo "<script>alert('Akun berhasil diaktivasi!'); window.location='login.php';</script>";
    } else {
        $tahap_otp = true;
        $pesan_status = "<div class='alert alert-danger'>Kode OTP salah!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b111e; color: white; }
        .form-control { background-color: #1e293b; border: 1px solid #334155; color: white; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container col-md-6">
        <form method="POST" class="p-4 bg-dark rounded shadow">
            <?php if (!$tahap_otp): ?>
                <h4 class="mb-3 text-warning">Registrasi Anggota Baru</h4>
                <?= $pesan_status; ?>
                <input type="text" name="nama" class="form-control mb-2" placeholder="Nama Lengkap" required>
                <input type="text" name="username" class="form-control mb-2" placeholder="Username untuk Login" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                <input type="text" name="no_hp" class="form-control mb-2" placeholder="No HP" required>
                <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                <textarea name="alamat" class="form-control mb-2" placeholder="Alamat"></textarea>
                <button type="submit" name="daftar" class="btn btn-primary w-100">Selesaikan Registrasi</button>
            <?php else: ?>
                <h4 class="mb-3 text-warning">Verifikasi OTP</h4>
                <?= $pesan_status; ?>
                <input type="text" name="otp_input" class="form-control mb-2" placeholder="Masukkan 6 Digit OTP" required>
                <button type="submit" name="verifikasi" class="btn btn-warning w-100">Verifikasi Akun</button>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>