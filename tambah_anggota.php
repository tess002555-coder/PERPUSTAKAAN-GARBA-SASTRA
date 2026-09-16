<?php
session_start();
include 'config/koneksi.php';

if (file_exists('catat_log.php')) { include 'catat_log.php'; }
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit; }

// Proteksi Akses Admin
$query = mysqli_query($conn, "SELECT * FROM user WHERE Username='" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'");
$user = mysqli_fetch_assoc($query);
if ($user['Role'] != 'admin' && ($user['akses_khusus'] != 1 || strtotime($user['batas_akses']) < time())) {
    die("Akses ditolak oleh admin!");
}

$pesan_sukses = "";
$tautan_wa = "";

if (isset($_POST['simpan'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_hp    = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $username_baru = mysqli_real_escape_string($conn, $_POST['username_baru']);
    $password_baru = md5($_POST['password_baru']); // Menggunakan enkripsi MD5 sesuai sistem Anda

    // Generate Kode Anggota Otomatis
    $kode_anggota = "AGT-" . date('Y') . "-" . rand(100, 999);
    // Generate OTP Otomatis 6 Digit
    $kode_otp = rand(100000, 999999);

    // Insert ke Database
    $query_insert = "INSERT INTO anggota (kode_anggota, Nama, Alamat, no_hp, email, username, password, otp, status_aktivasi) 
                     VALUES ('$kode_anggota', '$nama', '$alamat', '$no_hp', '$email', '$username_baru', '$password_baru', '$kode_otp', 'belum_aktif')";
    
    if (mysqli_query($conn, $query_insert)) {
        if (function_exists('catatLog')) { catatLog($_SESSION['username'], "Mendaftarkan anggota baru: $nama"); }

        // Format No HP ke standar WhatsApp (62)
        $no_hp_wa = $no_hp;
        if (substr($no_hp_wa, 0, 1) === '0') { $no_hp_wa = '62' . substr($no_hp_wa, 1); }

        // Membuat Link Aktivasi Otomatis (Arahkan ke IP server lokal Anda/domain Anda)
        $link_aktivasi = "http://localhost/PROJECT_PERPUSTAKAAN/aktivasi.php?kode=" . $kode_anggota;

        // Draft Pesan WA
        $pesan_wa = "Halo *$nama*,\n\nPendaftaran akun Perpustakaan Garba Sastra Anda berhasil.\n\n*Detail Akun Anda:*\n📌 Username: $username_baru\n\n*KODE OTP AKTIVASI:* $kode_otp\n\nSilakan klik link di bawah ini untuk mengaktifkan akun Anda:\n🔗 $link_aktivasi";
        $tautan_wa = "https://api.whatsapp.com/send?phone=" . $no_hp_wa . "&text=" . urlencode($pesan_wa);
        
        $pesan_sukses = "
        <div class='alert alert-success shadow mb-4'>
            <h4><i class='bi bi-check-circle-fill'></i> Pendaftaran Berhasil!</h4>
            <p>Akun <strong>$nama</strong> telah dibuat dengan status <span class='badge bg-danger'>Belum Aktif</span>.</p>
            <hr>
            <a href='$tautan_wa' target='_blank' class='btn btn-success fw-bold'><i class='bi bi-whatsapp'></i> Kirim Link Aktivasi & OTP via WA</a>
            <a href='anggota.php' class='btn btn-outline-secondary ms-2'>Kembali</a>
        </div>";
    } else {
        $pesan_sukses = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Anggota - Garba Sastra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>body { background-color: #0f172a; }.card-custom-dark { background-color: #1e293b !important; border: 1px solid #334155 !important; color: #f8fafc !important; }.form-control-dark { background-color: #0f172a !important; border: 1px solid #475569 !important; color: #ffffff !important; }</style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?= $pesan_sukses; ?>
            <?php if(empty($tautan_wa)): ?>
            <div class="card card-custom-dark shadow-lg border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-warning"><i class="bi bi-person-plus-fill"></i> Registrasi Anggota Baru</h3>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control form-control-dark" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat Email</label>
                                <input type="email" name="email" class="form-control form-control-dark" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Rumah</label>
                            <textarea name="alamat" rows="2" class="form-control form-control-dark" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Handphone (WhatsApp)</label>
                            <input type="text" name="no_hp" class="form-control form-control-dark" placeholder="Contoh: 0812345678" required>
                        </div>
                        <div class="row border-top border-secondary pt-3 mt-3">
                            <p class="text-warning small fw-bold">KREDENSIAL LOGIN ANGGOTA</p>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username Anggota</label>
                                <input type="text" name="username_baru" class="form-control form-control-dark" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Anggota</label>
                                <input type="password" name="password_baru" class="form-control form-control-dark" required>
                            </div>
                        </div>
                        <div class="d-flex gap-2 pt-3">
                            <button type="submit" name="simpan" class="btn btn-warning fw-bold text-dark px-4">Simpan & Buat OTP</button>
                            <a href="anggota.php" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>