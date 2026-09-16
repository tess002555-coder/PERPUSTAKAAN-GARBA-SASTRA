<?php
if (!function_exists('catatLog')) {
    function catatLog($username, $aktivitas) {
        global $conn;

        // Waktu saat ini
        $waktu = date('Y-m-d H:i:s');

        // Struktur QUERY disesuaikan dengan database lama Anda (tanpa ip_address)
        $stmt = $conn->prepare("INSERT INTO log_aktivitas (username, aktivitas, waktu) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $aktivitas, $waktu);
        $stmt->execute();
        $stmt->close();
    }
}
?>