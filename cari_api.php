<?php
header('Content-Type: application/json');

if (isset($_GET['q'])) {
    $query = urlencode($_GET['q']);
    $url = "https://www.googleapis.com/books/v1/volumes?q=" . $query . "&maxResults=1";

    // Opsi bypass SSL lokal jika XAMPP Anda belum dikonfigurasi HTTPS-nya
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $response = @file_get_contents($url, false, stream_context_create($arrContextOptions));
    
    if ($response !== false) {
        echo $response;
    } else {
        echo json_encode(['totalItems' => 0]);
    }
} else {
    echo json_encode(['totalItems' => 0]);
}
?>