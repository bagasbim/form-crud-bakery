<?php
include 'koneksi.php';

// Fungsi untuk menambahkan customer
if (isset($_POST['add_customer'])) {
  $nama_customer = $_POST['nama_customer'];

  $query = "INSERT INTO customer (nama_customer) VALUES ('$nama_customer')";
  mysqli_query($koneksi, $query);

  header('Location: customer.php');
  exit();
}

// Fungsi untuk menghapus customer
if (isset($_GET['delete_customer'])) {
  $id_customer = $_GET['delete_customer'];

  $query = "DELETE FROM customer WHERE id = $id_customer";
  mysqli_query($koneksi, $query);

  header('Location: customer.php');
  exit();
}

// Mendapatkan data customer dari database
$query = "SELECT * FROM customer";
$result = mysqli_query($koneksi, $query);
$customer = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Customer</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Customer</h1>
  
  <nav>
        <a href="index.php">[<-] Kembali ke Halaman Utama</a>
  </nav>

  <form action="" method="POST">
    <input type="text" name="nama_customer" placeholder="Nama Customer" required>
    <button type="submit" name="add_customer">Tambah</button>
  </form>

  <table>
    <tr>
      <th>ID</th>
      <th>Nama Customer</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($customer as $cust): ?>
      <tr>
        <td><?= $cust['id']; ?></td>
        <td><?= $cust['nama_customer']; ?></td>
        <td>
          <a href="customer.php?delete_customer=<?= $cust['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus customer ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
