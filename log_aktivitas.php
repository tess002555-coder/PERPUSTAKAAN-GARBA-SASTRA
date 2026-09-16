<?php
session_start();
include 'config/koneksi.php';

$query = mysqli_query(
    $conn,
    "SELECT * FROM log_aktivitas ORDER BY waktu DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
<title>Log Aktivitas</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-4">

<h3>Riwayat Aktivitas</h3>

<table class="table table-bordered">

<tr class="table-dark">
<th>No</th>
<th>User</th>
<th>Aktivitas</th>
<th>Waktu</th>
</tr>

<?php
$no=1;

while($data=mysqli_fetch_assoc($query)){
?>

<tr>

<td><?= $no++ ?></td>
<td><?= $data['username'] ?></td>
<td><?= $data['aktivitas'] ?></td>
<td><?= $data['waktu'] ?></td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>