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
$pdf->Cell(190, 10, 'LAPORAN DATA ANGGOTA', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 10, 'No', 1, 0, 'C');
$pdf->Cell(45, 10, 'Nama', 1);
$pdf->Cell(55, 10, 'Alamat', 1);
$pdf->Cell(35, 10, 'No HP', 1);
$pdf->Cell(45, 10, 'Email', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$no = 1;

$query = mysqli_query($conn, "SELECT * FROM anggota ORDER BY id DESC");
while ($data = mysqli_fetch_assoc($query)) {
    $pdf->Cell(10, 10, $no++, 1, 0, 'C');
    $pdf->Cell(45, 10, $data['Nama'], 1);
    $pdf->Cell(55, 10, $data['Alamat'], 1);
    $pdf->Cell(35, 10, $data['no_hp'], 1);
    $pdf->Cell(45, 10, $data['email'], 1);
    $pdf->Ln();
}

$pdf->Output();
?>