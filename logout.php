<?php
session_start();
include 'config/koneksi.php';
include 'catat_log.php';

// Jika sesi username terdeteksi, catat aktivitas keluar sebelum dihancurkan
if (isset($_SESSION['username'])) {
    catatLog($_SESSION['username'], "Keluar dari sistem (Logout)");
}

// Mengosongkan data session
$_SESSION = array();

// Hancurkan cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session database secara total
session_destroy();

// Redirect otomatis ke halaman login
header("Location: login.php");
exit;
?>