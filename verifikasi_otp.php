<?php
session_start();
include 'config/koneksi.php';

// Jika tidak ada sesi pendaftaran, tendang balik ke halaman register
if (!isset($_SESSION['registrasi_kode_anggota'])) {
    header("Location: register.php");
    exit;
}

$kode_anggota = $_SESSION['registrasi_kode_anggota'];
$nama_user    = $_SESSION['registrasi_nama'];
$link_wa      = $_SESSION['tautan_otp_wa'];
$pesan_status = "";

if (isset($_POST['verifikasi'])) {
    $otp_input = mysqli_real_escape_string($conn, $_POST['otp']);

    // Cocokkan kode OTP berdasarkan kode anggota di DB
    $cek = mysqli_query($conn, "SELECT * FROM anggota WHERE kode_anggota='$kode_anggota' AND otp='$otp_input'");

    if (mysqli_num_rows($cek) > 0) {
        // Jika cocok, ubah status menjadi aktif dan hapus sisa token OTP-nya
        mysqli_query($conn, "UPDATE anggota SET status_aktivasi='aktif', otp='' WHERE kode_anggota='$kode_anggota'");
        
        // Bersihkan session pendaftaran
        unset($_SESSION['registrasi_kode_anggota']);
        unset($_SESSION['registrasi_nama']);
        unset($_SESSION['tautan_otp_wa']);

        $pesan_status = "
        <div class='alert alert-success text-center shadow mb-4'>
            <h4>🎉 Aktivasi Akun Berhasil!</h4>
            <p class='mb-0'>Akun Anda telah aktif sepenuhnya. Silakan menuju halaman login untuk masuk ke aplikasi.</p>
            <a href='login.php' class='btn btn-primary fw-bold mt-3 px-4'>Masuk ke Aplikasi</a>
        </div>";
    } else {
        $pesan_status = "<div class='alert alert-danger text-center'>❌ Kode OTP salah atau tidak valid! Silakan periksa kembali pesan Anda.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #0f172a; color: white; }
        .tracking-widest { letter-spacing: 0.25em; }
    </style>
</head>
<body class="d-flex align-items-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            
            <?= $pesan_status; ?>

            <?php if (!empty($_SESSION['registrasi_kode_anggota'])): ?>
            <div class="card bg-dark text-white border-secondary shadow-lg" style="border-radius: 12px;">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-shield-check text-warning display-4 mb-2"></i>
                    <h4 class="text-warning fw-bold mb-1">Verifikasi Kode OTP</h4>
                    <p class="text-muted small mb-4">Halo <strong><?= htmlspecialchars($nama_user) ?></strong>, pendaftaran Anda berhasil disimpan dengan kode <strong><?= $kode_anggota ?></strong>.</p>
                    
                    <div class="alert alert-info border-0 text-start small mb-4">
                        <i class="bi bi-info-circle-fill"></i> <strong>Penting:</strong> Untuk mendapatkan/menerima nomor token verifikasi, silakan klik tombol ambil di bawah ini:
                        <div class="d-grid mt-2">
                            <a href="<?= $link_wa ?>" target="_blank" class="btn btn-sm btn-success fw-bold"><i class="bi bi-whatsapp"></i> Ambil OTP via WhatsApp</a>
                        </div>
                    </div>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-start d-block mb-2">Masukkan 6 Digit OTP</label>
                            <input type="text" name="otp" maxlength="6" class="form-control text-center fs-2 fw-bold tracking-widest bg-black text-warning border-secondary" placeholder="000000" required autocomplete="off">
                        </div>
                        <button type="submit" name="verifikasi" class="btn btn-warning w-100 fw-bold text-dark py-2.5">
                            Verifikasi & Aktifkan Akun
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>