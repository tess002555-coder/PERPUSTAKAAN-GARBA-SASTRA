<?php
include 'config/koneksi.php';

$kode_get = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
$pesan = "";

if (isset($_POST['aktivasi'])) {
    $kode_anggota = mysqli_real_escape_string($conn, $_POST['kode_anggota']);
    $otp_input    = mysqli_real_escape_string($conn, $_POST['otp']);

    // Cek kecocokan OTP di Database
    $cek = mysqli_query($conn, "SELECT * FROM anggota WHERE kode_anggota='$kode_anggota' AND otp='$otp_input'");
    
    if (mysqli_num_rows($cek) > 0) {
        // Update status menjadi aktif jika OTP valid
        mysqli_query($conn, "UPDATE anggota SET status_aktivasi='aktif', otp='' WHERE kode_anggota='$kode_anggota'");
        $pesan = "<div class='alert alert-success text-center shadow'>🎉 Akun Anda Berhasil Diaktifkan! Silakan login di aplikasi.</div>";
    } else {
        $pesan = "<div class='alert alert-danger text-center shadow'>❌ Kode OTP yang Anda masukkan salah / tidak valid!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aktivasi Akun Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #0f172a; color: white; }</style>
</head>
<body class="d-flex align-items-center" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <?= $pesan; ?>
            <div class="card bg-dark text-white border-secondary shadow-lg">
                <div class="card-body p-4">
                    <h4 class="text-center text-warning mb-3">Verifikasi OTP Anggota</h4>
                    <p class="text-center text-muted small">Masukkan Kode OTP yang dikirimkan oleh Admin melalui WhatsApp Anda.</p>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Kode Anggota</label>
                            <input type="text" name="kode_anggota" class="form-control bg-secondary text-white fw-bold" value="<?= $kode_get; ?>" required readonly>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Masukkan 6 Digit OTP</label>
                            <input type="text" name="otp" maxlength="6" class="form-control text-center fs-3 fw-bold tracking-widest" placeholder="000000" required autocomplete="off">
                        </div>
                        <button type="submit" name="aktivasi" class="btn btn-warning w-100 fw-bold text-dark py-2">Verifikasi & Aktifkan Akun</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>