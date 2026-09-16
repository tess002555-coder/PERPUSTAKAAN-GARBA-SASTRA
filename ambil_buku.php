<?php

header('Content-Type: application/json');

if (!isset($_GET['q'])) {

    echo json_encode([
        "status" => "error"
    ]);

    exit;
}

$q = urlencode($_GET['q']);

$url = "https://openlibrary.org/search.json?q=" . $q;

$response = file_get_contents($url);

if ($response === false) {

    echo json_encode([
        "status" => "error",
        "pesan" => "Tidak dapat mengambil data"
    ]);

    exit;
}

$data = json_decode($response, true);

if (empty($data['docs'])) {

    echo json_encode([
        "status" => "kosong"
    ]);

    exit;
}

$buku = $data['docs'][0];

echo json_encode([

    "title" =>
    $buku['title'] ?? "",

    "author" =>
    isset($buku['author_name'])
    ? implode(", ", $buku['author_name'])
    : "",

    "publisher" =>
    isset($buku['publisher'][0])
    ? $buku['publisher'][0]
    : "-",

    "category" =>
    isset($buku['subject'][0])
    ? $buku['subject'][0]
    : "-",

    "cover" =>
    isset($buku['cover_i'])
    ? "https://covers.openlibrary.org/b/id/" . $buku['cover_i'] . "-L.jpg"
    : "",

    "tahun" =>
    isset($buku['first_publish_year'])
    ? $buku['first_publish_year']
    : ""

]);
?>