<?php
session_start();
include 'config/koneksi.php';

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

// ==========================================
// 1. PROSES MASUK / LOG IN
// ==========================================
if (isset($_POST['proses_login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Cek di tabel 'user' (untuk admin/petugas)
    $query_user = mysqli_query($conn, "SELECT Username, Role FROM user WHERE Username = '$username' AND Password = '$password'");
    
    if (mysqli_num_rows($query_user) > 0) {
        $data = mysqli_fetch_assoc($query_user);
        $_SESSION['username'] = $data['Username'];
        $_SESSION['role']     = $data['Role']; // <-- PASTIKAN BARIS INI ADA
        
        echo "<script>alert('Selamat datang, " . $data['Username'] . " (" . $data['Role'] . ")!'); window.location='dashboard.php';</script>";
        exit;
    } 
    // Jika tidak ada di 'user', cek di tabel 'anggota'
    else {
        $query_anggota = mysqli_query($conn, "SELECT Username, 'anggota' as Role FROM anggota WHERE Username = '$username' AND Password = '$password'");
        
        if (mysqli_num_rows($query_anggota) > 0) {
            $data = mysqli_fetch_assoc($query_anggota);
            $_SESSION['username'] = $data['Username'];
            $_SESSION['role']     = 'anggota'; // <-- PASTIKAN BARIS INI ADA
            
            echo "<script>alert('Selamat datang, " . $data['Username'] . "!'); window.location='dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Username atau Password salah!');</script>";
        }
    }
}

// ==========================================
// 2. PROSES DAFTAR AKUN BARU
// ==========================================
if (isset($_POST['proses_daftar'])) {
    $role_daftar = $_POST['role_daftar'];

    if ($role_daftar == 'petugas') {
        $username_baru = mysqli_real_escape_string($conn, $_POST['reg_username']);
        $password_baru = mysqli_real_escape_string($conn, $_POST['reg_password']);
        $insert_petugas = mysqli_query($conn, "INSERT INTO user (Username, Password, Role, akses_khusus) VALUES ('$username_baru', '$password_baru', 'petugas', 0)");
        if ($insert_petugas) echo "<script>alert('Pendaftaran Petugas Berhasil!');</script>";
    } else {
        $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $username = mysqli_real_escape_string($conn, $_POST['reg_username']); // Tambahan
        $password = mysqli_real_escape_string($conn, $_POST['reg_password']); // Tambahan
        $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
        $no_hp    = mysqli_real_escape_string($conn, $_POST['no_hp']);
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $kode_anggota = "AGT-" . date('Y') . "-" . rand(100, 999);

        // Pastikan tabel 'anggota' sudah memiliki kolom 'Username' dan 'Password'
        $insert = mysqli_query($conn, "INSERT INTO anggota (kode_anggota, Nama, Username, Password, Alamat, no_hp, email) VALUES ('$kode_anggota', '$nama', '$username', '$password', '$alamat', '$no_hp', '$email')");
        if ($insert) echo "<script>alert('Pendaftaran Berhasil! Kode Anda: $kode_anggota');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: #1e293b; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5); max-width: 900px; width: 95%; }
        .side-visual { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 40px; color: white; }
        .form-box { padding: 40px; }
        .nav-pills .nav-link { color: #94a3b8; font-weight: 600; }
        .nav-pills .nav-link.active { background-color: #3b82f6 !important; color: white; }
        .form-control { background-color: #0f172a; border: 1px solid #334155; color: white; }
        .form-control:focus { background-color: #0f172a; border-color: #3b82f6; color: white; }
        /* Update pada bagian ini di CSS Anda */
.form-control, .form-select { 
    background-color: #111827 !important; /* Warna background gelap */
    border: 1px solid #374151 !important; 
    color: #ffffff !important; /* MENGUBAH WARNA TEKS MENJADI PUTIH */
}

/* Memastikan placeholder juga terlihat */
.form-control::placeholder {
    color: #6b7280 !important; /* Warna abu-abu agar placeholder tetap lembut */
}

/* Memastikan pilihan pada dropdown terlihat putih */
.form-select option {
    background-color: #111827;
    color: #ffffff;
}
    </style>
</head>
<body>

<div class="login-box row g-0">
    <div class="col-md-5 side-visual d-none d-md-flex flex-column justify-content-center">
        <h2>Jendela Dunia Terbuka di Sini ✨</h2>
        <p>Buku adalah jembatan terbaik yang menghubungkan masa lalu dengan masa depan.</p>
    </div>

    <div class="col-md-7 form-box">
        <ul class="nav nav-pills mb-4 justify-content-center bg-dark rounded-pill p-1" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#login">Masuk</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#daftar">Registrasi</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="login">
                <form method="POST">
                    <div class="mb-3"><label class="text-secondary">Username</label><input type="text" name="username" class="form-control" required></div>
                    <div class="mb-3"><label class="text-secondary">Password</label><input type="password" name="password" class="form-control" required></div>
                    <button type="submit" name="proses_login" class="btn btn-primary w-100">Masuk</button>
                </form>
            </div>

            <div class="tab-pane fade" id="daftar">
                <form method="POST">
                    <select name="role_daftar" id="role" class="form-select mb-3" onchange="toggleForm()">
                        <option value="anggota">Anggota Biasa</option>
                        <option value="petugas">Petugas</option>
                    </select>
                    
                    <div id="dynamic-fields">
                        <input type="text" name="nama_lengkap" class="form-control mb-2" placeholder="Nama Lengkap" required>
                        <input type="text" name="reg_username" class="form-control mb-2" placeholder="Username (Untuk Login)" required>
                        <input type="password" name="reg_password" class="form-control mb-2" placeholder="Password (Untuk Login)" required>
                        <input type="text" name="no_hp" class="form-control mb-2" placeholder="Nomor HP">
                        <input type="email" name="email" class="form-control mb-2" placeholder="Email">
                        <textarea name="alamat" class="form-control" placeholder="Alamat"></textarea>
                    </div>
                    <button type="submit" name="proses_daftar" class="btn btn-success w-100 mt-3">Selesaikan Registrasi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleForm() {
    // Logika tambahan bisa ditambahkan di sini untuk menyembunyikan field yang tidak perlu untuk petugas
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>