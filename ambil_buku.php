<?php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'offline',
    'pesan' => 'Mode offline aktif. Pencarian buku dari internet tidak tersedia. Silakan isi data buku secara manual.'
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
