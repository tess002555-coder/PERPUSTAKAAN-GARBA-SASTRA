<?php
session_start();
require_once __DIR__ . '/config/koneksi.php';

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function require_login(): void {
    if (empty($_SESSION['username'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user(mysqli $conn): ?array {
    require_login();
    $username = mysqli_real_escape_string($conn, $_SESSION['username']);
    $result = mysqli_query($conn, "SELECT * FROM user WHERE Username='$username' LIMIT 1");
    return $result ? mysqli_fetch_assoc($result) : null;
}

function require_library_access(mysqli $conn): array {
    $user = current_user($conn);
    if (!$user) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }

    $role = strtolower((string)($user['Role'] ?? $user['role'] ?? 'petugas'));
    $akses = (int)($user['akses_khusus'] ?? 0);
    $batas = $user['batas_akses'] ?? null;
    $akses_aktif = $akses === 1 && $batas && strtotime($batas) > time();

    if ($role !== 'admin' && !$akses_aktif) {
        http_response_code(403);
        exit('Akses ditolak oleh admin!');
    }

    $_SESSION['role'] = $role;
    $_SESSION['akses_khusus'] = $akses;
    $_SESSION['batas_akses'] = $batas;

    return $user;
}
