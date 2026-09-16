<?php
session_start();
include 'config/koneksi.php';
include 'config/cek_hak_akses.php'; // <-- WAJIB disisipkan di sini

// Kode proses database bawaan Anda di bawah...
// $query = mysqli_query($conn, "INSERT INTO buku ...");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 1. Cek apakah data transaksi benar-benar ada dan statusnya masih 'Pinjam'
    // Disesuaikan dengan struktur kolom database Anda
    $query = mysqli_query($conn, "SELECT * FROM peminjaman WHERE id = '$id' AND status = 'Pinjam'");
    if (mysqli_num_rows($query) == 0) {
        echo "<script>alert('Data transaksi tidak ditemukan atau sudah dikembalikan!'); window.location='peminjaman.php';</script>";
        exit;
    }

    $data = mysqli_fetch_assoc($query);
    $id_buku         = $data['id_buku'];
    $tanggal_kembali = $data['tanggal_kembali'];
    $judul_buku      = $data['Judul'];
    $nama_anggota    = $data['Nama'];

    // 2. Hitung Logika Keterlambatan & Denda Otomatis
    $tanggal_sekarang = date('Y-m-d');
    $denda = 0;
    $tarif_denda = 1000; // Tarif denda Rp 1.000 per hari keterlambatan

    // Jika tanggal hari ini melewati tanggal seharusnya kembali
    if (strtotime($tanggal_sekarang) > strtotime($tanggal_kembali)) {
        $selisih = strtotime($tanggal_sekarang) - strtotime($tanggal_kembali);
        $hari_terlambat = floor($selisih / (60 * 60 * 24)); // Konversi detik ke hari
        $denda = $hari_terlambat * $tarif_denda;
    }

    // 3. Mulai Database Transaction (ACID) agar sinkronisasi dua tabel aman
    mysqli_begin_transaction($conn);

    try {
        // Update status menjadi 'Kembali' dan isi nominal denda (jika ada)
        $stmt_kembali = $conn->prepare("UPDATE peminjaman SET status = 'Kembali', denda = ? WHERE id = ?");
        $stmt_kembali->bind_param("ii", $denda, $id);
        $stmt_kembali->execute();

        // Tambah kembali stok buku sebanyak 1 angka di tabel buku
        $stmt_update_stok = $conn->prepare("UPDATE buku SET Stok = Stok + 1 WHERE id = ?");
        $stmt_update_stok->bind_param("i", $id_buku);
        $stmt_update_stok->execute();

        // Jika kedua perintah di atas sukses, simpan permanen ke database
        mysqli_commit($conn);

        // Catat ke log aktivitas sistem
        if (function_exists('catatLog')) {
            $log_msg = "Menerima pengembalian buku: '" . $judul_buku . "' dari " . $nama_anggota . " (Denda: Rp " . number_format($denda, 0, ',', '.') . ")";
            catatLog($_SESSION['username'], $log_msg);
        }

        // Tampilkan notifikasi yang interaktif ke admin
        if ($denda > 0) {
            echo "<script>
                    alert('Buku berhasil dikembalikan! Anggota terlambat " . $hari_terlambat . " hari. Total Denda yang harus dibayar: Rp " . number_format($denda, 0, ',', '.') . "');
                    window.location='peminjaman.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Buku berhasil dikembalikan tepat waktu! Terima kasih.');
                    window.location='peminjaman.php';
                  </script>";
        }

    } catch (Exception $e) {
        // Jika salah satu query gagal, batalkan seluruh perubahan agar data tidak korup
        mysqli_rollback($conn);
        echo "<script>alert('Gagal memproses pengembalian: " . $e->getMessage() . "'); window.location='peminjaman.php';</script>";
    }

    $stmt_kembali->close();
    $stmt_update_stok->close();
} else {
    header("Location: peminjaman.php");
}
exit;
?>