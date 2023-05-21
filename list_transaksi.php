<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>List Transaksi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>List Transaksi</h2>
  
  <nav>
        <a href="index.php">[<-] Kembali ke Halaman Utama</a>
  </nav>

  <table>
    <tr>
      <th>ID</th>
      <th>Nama Customer</th>
      <th>Tanggal Transaksi</th>
      <th>Total Belanja</th>
      <th>Aksi</th>
    </tr>
    <?php
    $query_transaksi = "SELECT transaksi.id, customer.nama_customer, transaksi.tanggal_transaksi, SUM(detail_transaksi.qty * detail_transaksi.harga) AS total_belanja FROM transaksi LEFT JOIN customer ON transaksi.customer_id = customer.id LEFT JOIN detail_transaksi ON transaksi.id = detail_transaksi.transaksi_id GROUP BY transaksi.id";
    $result_transaksi = mysqli_query($koneksi, $query_transaksi);
    $transaksi = mysqli_fetch_all($result_transaksi, MYSQLI_ASSOC);
    foreach ($transaksi as $item):
      ?>
      <tr>
        <td><?= $item['id']; ?></td>
        <td><?= $item['nama_customer']; ?></td>
        <td><?= $item['tanggal_transaksi']; ?></td>
        <td><?= $item['total_belanja']; ?></td>
        <td>
          <a href="transaksi.php?delete_transaksi=<?= $item['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
