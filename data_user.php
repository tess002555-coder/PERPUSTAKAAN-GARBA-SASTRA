```php
<?php
session_start();
include 'config/koneksi.php';

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

$query = mysqli_query($conn,"
SELECT * FROM user
WHERE Username LIKE '%$keyword%'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>

<title>Data User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body class="bg-dark">

<div class="container-fluid">

<div class="row">

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Konten -->
<div class="col-md-10">

<div class="container mt-4">

<h2 class="text-white mb-4">

<i class="bi bi-person-gear"></i>
Data User

</h2>

<div class="card bg-dark text-white shadow">

<div class="card-body">

<div class="row mb-3">

<div class="col-md-6">

<form method="GET">

<div class="input-group">

<input
type="text"
name="keyword"
class="form-control"
placeholder="Cari Username"
value="<?= $keyword ?>">

<button class="btn btn-warning">

<i class="bi bi-search"></i>

</button>

</div>

</form>

</div>

<div class="col-md-6 text-end">

<a href="tambah_user.php"
class="btn btn-primary">

<i class="bi bi-plus-circle"></i>
Tambah User

</a>

</div>

</div>

<div class="table-responsive">

<table class="table table-dark table-bordered table-hover">

<tr>

<th>No</th>
<th>Username</th>
<th>Role</th>
<th>Status Akses</th>
<th>Berlaku Sampai</th>
<th>Aksi</th>

</tr>

<?php
$no = 1;

while($data=mysqli_fetch_assoc($query)){
?>

<tr>

<td><?= $no++; ?></td>

<td><?= $data['Username']; ?></td>

<td>

<?php if($data['Role']=="admin"){ ?>

<span class="badge bg-danger">

Admin

</span>

<?php } else { ?>

<span class="badge bg-primary">

Petugas

</span>

<?php } ?>

</td>

<td>

<?php if($data['akses_khusus']==1){ ?>

<span class="badge bg-success">

Aktif

</span>

<?php } else { ?>

<span class="badge bg-danger">

Tidak Aktif

</span>

<?php } ?>

</td>

<td>

<?= $data['batas_akses']; ?>

</td>

<td>

<a href="beri_akses.php?id=<?= $data['id']; ?>"
class="btn btn-success btn-sm">

<i class="bi bi-check-circle"></i>

</a>

<a href="cabut_akses.php?id=<?= $data['id']; ?>"
class="btn btn-danger btn-sm">

<i class="bi bi-x-circle"></i>

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

</body>
</html>
```
