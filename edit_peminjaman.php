<?php
session_start();
include 'config/koneksi.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
}

$id = $_GET['id'];

$query = mysqli_query($conn,
"SELECT * FROM peminjaman WHERE id='$id'");

$data = mysqli_fetch_assoc($query);

if(isset($_POST['update'])){

    $status_lama = $data['status'];
    $status_baru = $_POST['status'];

    $tanggal_kembali = $data['tanggal_kembali'];

    $tanggal_sekarang = date("Y-m-d");

    $denda = 0;

    $tanggal_sekarang = date('Y-m-d');
    $denda = 0;

    if($status_baru=="Dikembalikan"){

        if($tanggal_sekarang > $tanggal_kembali){

            $terlambat =
            (strtotime($tanggal_sekarang)-strtotime($tanggal_kembali))
            /(60*60*24);

            $denda = $terlambat * 1000;

        }

    }

    if($tanggal_sekarang > $tanggal_kembali){

        $terlambat =
        (strtotime($tanggal_sekarang)-strtotime($tanggal_kembali))
        /(60*60*24);

        $denda = $terlambat * 1000;

}

    mysqli_query($conn,
    "UPDATE peminjaman
    SET
    status='$status_baru',
    denda='$denda'
    WHERE id='$id'");

    if($status_lama == "Dipinjam" && $status_baru == "Dikembalikan"){

        mysqli_query($conn,
        "UPDATE buku
        SET Stok = Stok + 1
        WHERE id='".$data['id_buku']."'");
    }

    header("Location: peminjaman.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Peminjaman</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-body">

<h2>Edit Status Peminjaman</h2>

<form method="POST">

<div class="mb-3">

<label>Status</label>

<select name="status" class="form-control">

<option value="Dipinjam"
<?php if($data['status']=="Dipinjam") echo "selected"; ?>>
Dipinjam
</option>

<option value="Dikembalikan"
<?php if($data['status']=="Dikembalikan") echo "selected"; ?>>
Dikembalikan
</option>

</select>

</div>

<button type="submit"
name="update"
class="btn btn-success">
Update
</button>

<a href="peminjaman.php"
class="btn btn-secondary">
Kembali
</a>

</form>

</div>

</div>

</div>

</body>
</html>