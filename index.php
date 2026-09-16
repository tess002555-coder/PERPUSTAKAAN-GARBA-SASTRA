<?php
session_start();

// Routing Otomatis: Jika user sudah login bawa ke dashboard, jika belum bawa ke login
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>