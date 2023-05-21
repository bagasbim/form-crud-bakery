<?php
$host = "localhost";
$username = "root";
$password = "P@ssw0rd";
$database = "bakery_db";

$koneksi = mysqli_connect($host, $username, $password, $database);

if (mysqli_connect_errno()) {
  echo "Koneksi database gagal: " . mysqli_connect_error();
  exit();
}
?>
