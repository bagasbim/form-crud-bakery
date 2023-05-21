<?php
include 'koneksi.php';

// Fungsi untuk menambahkan kue
if (isset($_POST['add_kue'])) {
  $nama_kue = $_POST['nama_kue'];
  $jenis_kue_id = $_POST['jenis_kue_id'];
  $qty = $_POST['qty'];
  $harga_jual = $_POST['harga_jual'];

  $query = "INSERT INTO kue (nama_kue, jenis_kue_id, qty, harga_jual) VALUES ('$nama_kue', '$jenis_kue_id', '$qty', '$harga_jual')";
  mysqli_query($koneksi, $query);

  header('Location: kue.php');
  exit();
}

// Fungsi untuk menghapus kue
if (isset($_GET['delete_kue'])) {
  $id_kue = $_GET['delete_kue'];

  $query = "DELETE FROM kue WHERE id = $id_kue";
  mysqli_query($koneksi, $query);

  header('Location: kue.php');
  exit();
}

// Mendapatkan data kue dari database
$query = "SELECT kue.id, kue.nama_kue, jenis_kue.nama AS jenis_kue, kue.qty, kue.harga_jual FROM kue JOIN jenis_kue ON kue.jenis_kue_id = jenis_kue.id";
$result = mysqli_query($koneksi, $query);
$kue = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Mendapatkan data jenis kue dari database
$query_jenis = "SELECT * FROM jenis_kue";
$result_jenis = mysqli_query($koneksi, $query_jenis);
$jenis_kue = mysqli_fetch_all($result_jenis, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Kue</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Kue</h1>
  <nav>
        <a href="index.php">[<-] Kembali ke Halaman Utama</a>
   </nav>

  <form action="" method="POST">
    <input type="text" name="nama_kue" placeholder="Nama Kue" required>
    <select name="jenis_kue_id">
      <?php foreach ($jenis_kue as $jenis): ?>
        <option value="<?= $jenis['id']; ?>"><?= $jenis['nama']; ?></option>
      <?php endforeach; ?>
    </select>
    <input type="number" name="qty" placeholder="Qty" required>
    <input type="number" name="harga_jual" placeholder="Harga Jual" required>
    <button type="submit" name="add_kue">Tambah</button>
  </form>

  <table>
    <tr>
      <th>ID</th>
      <th>Nama Kue</th>
      <th>Jenis Kue</th>
      <th>Qty</th>
      <th>Harga Jual</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($kue as $item): ?>
      <tr>
        <td><?= $item['id']; ?></td>
        <td><?= $item['nama_kue']; ?></td>
        <td><?= $item['jenis_kue']; ?></td>
        <td><?= $item['qty']; ?></td>
        <td><?= $item['harga_jual']; ?></td>
        <td>
          <a href="kue.php?delete_kue=<?= $item['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus kue ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
