<?php
include 'koneksi.php';

// Fungsi untuk menambahkan jenis kue
if (isset($_POST['add_jenis_kue'])) {
  $nama_jenis = $_POST['nama_jenis'];

  $query = "INSERT INTO jenis_kue (nama) VALUES ('$nama_jenis')";
  mysqli_query($koneksi, $query);

  header('Location: jenis_kue.php');
  exit();
}

// Fungsi untuk menghapus jenis kue
if (isset($_GET['delete_jenis_kue'])) {
  $id_jenis = $_GET['delete_jenis_kue'];

  $query = "DELETE FROM jenis_kue WHERE id = $id_jenis";
  mysqli_query($koneksi, $query);

  header('Location: jenis_kue.php');
  exit();
}

// Mendapatkan data jenis kue dari database
$query = "SELECT * FROM jenis_kue";
$result = mysqli_query($koneksi, $query);
$jenis_kue = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Jenis Kue</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Jenis Kue</h1>
  
	<nav>
        <a href="index.php">[<-] Kembali ke Halaman Utama</a>
    </nav>

  <form action="" method="POST">
    <input type="text" name="nama_jenis" placeholder="Nama Jenis Kue" required>
    <button type="submit" name="add_jenis_kue">Tambah</button>
  </form>

  <table>
    <tr>
      <th>ID</th>
      <th>Nama Jenis Kue</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($jenis_kue as $jenis): ?>
      <tr>
        <td><?= $jenis['id']; ?></td>
        <td><?= $jenis['nama']; ?></td>
        <td>
          <a href="jenis_kue.php?delete_jenis_kue=<?= $jenis['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis kue ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
