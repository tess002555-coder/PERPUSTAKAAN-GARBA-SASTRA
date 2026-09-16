<?php
header('Content-Type: application/json');

if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search = urlencode($_GET['q']);
    $url = "https://www.googleapis.com/books/v1/volumes?q=" . $search . "&maxResults=1";

    // Inisialisasi cURL (Lebih kuat daripada file_get_contents)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    
    // Bypass Verifikasi SSL XAMPP lokal agar tidak diblokir Windows
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // Set timeout batas tunggu koneksi (5 detik)
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // Jika cURL eror, kirimkan detail erornya ke Javascript untuk ditampilkan
        $error_msg = curl_error($ch);
        echo json_encode([
            'totalItems' => 0, 
            'error_status' => true, 
            'message' => 'Eror Server Lokal: ' . $error_msg
        ]);
    } else {
        echo $response;
    }
    
    curl_close($ch);
} else {
    echo json_encode(['totalItems' => 0]);
}
?>