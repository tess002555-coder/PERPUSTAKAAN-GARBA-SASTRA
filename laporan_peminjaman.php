<?php
session_start();
if (!isset($_SESSION['username'])) {
    die("Akses ditolak! Anda harus login terlebih dahulu untuk mengunduh laporan.");
}

require('fpdf/fpdf.php');
include 'config/koneksi.php';

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'LAPORAN DATA PEMINJAMAN', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 10, 'No', 1, 0, 'C');
$pdf->Cell(45, 10, 'Nama Anggota', 1);
$pdf->Cell(50, 10, 'Judul Buku', 1);
$pdf->Cell(30, 10, 'Pinjam', 1, 0, 'C');
$pdf->Cell(30, 10, 'Kembali', 1, 0, 'C');
$pdf->Cell(25, 10, 'Status', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$no = 1;

$query = mysqli_query($conn, "
    SELECT peminjaman.*, anggota.Nama, buku.Judul 
    FROM peminjaman
    JOIN anggota ON anggota.id = peminjaman.id_anggota
    JOIN buku ON buku.id = peminjaman.id_buku
    ORDER BY peminjaman.id DESC
");

while ($data = mysqli_fetch_assoc($query)) {
    $pdf->Cell(10, 10, $no++, 1, 0, 'C');
    $pdf->Cell(45, 10, $data['Nama'], 1);
    $pdf->Cell(50, 10, $data['Judul'], 1);
    $pdf->Cell(30, 10, $data['tanggal_pinjam'], 1, 0, 'C');
    $pdf->Cell(30, 10, $data['tanggal_kembali'], 1, 0, 'C');
    $pdf->Cell(25, 10, $data['status'], 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output();
?>