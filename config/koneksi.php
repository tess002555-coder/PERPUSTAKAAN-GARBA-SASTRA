<?php

$conn = mysqli_connect(
    '127.0.0.1',
    'root',
    '',
    'PROJECT PERPUSTAKAAN',
    3307
);

if (!$conn) {
    die('Koneksi gagal : ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
