<?php
include 'koneksi.php';

// Fungsi untuk menambahkan transaksi
if (isset($_POST['add_transaksi'])) {
  $customer_id = $_POST['customer_id'];
  $nama_customer = $_POST['nama_customer'];
  $tanggal_transaksi = $_POST['tanggal_transaksi'];

  // Mendapatkan total belanja dari form
  $total_belanja = $_POST['total_belanja'];

  $query = "INSERT INTO transaksi (customer_id, nama_customer, tanggal_transaksi) VALUES ('$customer_id', '$nama_customer', '$tanggal_transaksi')";
  mysqli_query($koneksi, $query);

  // Mendapatkan ID transaksi terakhir yang ditambahkan
  $transaksi_id = mysqli_insert_id($koneksi);

  // Simpan detail transaksi
  $kue_ids = $_POST['kue_id'];
  $qtys = $_POST['qty'];
  $hargas = $_POST['harga'];

  $jumlah_kue = count($kue_ids);
  for ($i = 0; $i < $jumlah_kue; $i++) {
    $kue_id = $kue_ids[$i];
    $qty = $qtys[$i];
    $harga = $hargas[$i];

    $query_detail = "INSERT INTO detail_transaksi (transaksi_id, kue_id, qty, harga) VALUES ('$transaksi_id', '$kue_id', '$qty', '$harga')";
    mysqli_query($koneksi, $query_detail);
  }

  header('Location: transaksi.php');
  exit();
}

// Fungsi untuk menghapus transaksi
if (isset($_GET['delete_transaksi'])) {
  $id_transaksi = $_GET['delete_transaksi'];

  // Hapus detail transaksi terlebih dahulu
  $query_detail = "DELETE FROM detail_transaksi WHERE transaksi_id = $id_transaksi";
  mysqli_query($koneksi, $query_detail);

  // Hapus transaksi
  $query = "DELETE FROM transaksi WHERE id = $id_transaksi";
  mysqli_query($koneksi, $query);

  header('Location: transaksi.php');
  exit();
}

// Mendapatkan data customer dari database
$query_customer = "SELECT * FROM customer";
$result_customer = mysqli_query($koneksi, $query_customer);
$customer = mysqli_fetch_all($result_customer, MYSQLI_ASSOC);

// Mendapatkan data kue dari database
$query_kue = "SELECT * FROM kue";
$result_kue = mysqli_query($koneksi, $query_kue);
$kue = mysqli_fetch_all($result_kue, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Transaksi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Transaksi</h1>
  
  <nav>
        <a href="index.php">[<-] Kembali ke Halaman Utama</a>
  </nav>

  <form action="" method="POST">
    <label for="customer_id">Customer:</label>
    <select name="customer_id" id="customer_id">
      <option value="0">Walk In Customer</option>
      <?php foreach ($customer as $cust): ?>
        <option value="<?= $cust['id']; ?>"><?= $cust['nama_customer']; ?></option>
      <?php endforeach; ?>
    </select>
    <br><br>
    <input type="text" name="nama_customer" placeholder="Nama Customer" required>
    <br><br>
    <label for="tanggal_transaksi">Tanggal Transaksi:</label>
    <input type="date" name="tanggal_transaksi" required>
    <br><br>
    <table>
      <tr>
        <th>Kue</th>
        <th>Qty</th>
        <th>Harga</th>
        <th>Total</th>
      </tr>
      <?php foreach ($kue as $item): ?>
        <tr>
          <td><?= $item['nama_kue']; ?></td>
          <td><input type="number" name="qty[]" min="1" required></td>
          <td><?= $item['harga_jual']; ?></td>
          <td id="total"><?= $item['harga_jual']; ?></td>
          <input type="hidden" name="kue_id[]" value="<?= $item['id']; ?>">
          <input type="hidden" name="harga[]" value="<?= $item['harga_jual']; ?>">
        </tr>
      <?php endforeach; ?>
    </table>
    <br>
    <label for="total_belanja">Total Belanja:</label>
    <input type="text" name="total_belanja" id="total_belanja" readonly>
    <br><br>
    <button type="submit" name="add_transaksi">Tambah</button>
  </form>

  <script>
    // Menghitung total belanja saat mengubah nilai qty
    var qtys = document.getElementsByName("qty[]");
    for (var i = 0; i < qtys.length; i++) {
      qtys[i].addEventListener("input", function() {
        var qty = parseInt(this.value);
        var harga = parseInt(this.parentNode.nextElementSibling.innerHTML);
        var total = qty * harga;
        this.parentNode.nextElementSibling.nextElementSibling.innerHTML = total;

        // Menghitung total belanja keseluruhan
        var totals = document.querySelectorAll("#total");
        var totalBelanja = 0;
        for (var j = 0; j < totals.length; j++) {
          totalBelanja += parseInt(totals[j].innerHTML);
        }
        document.getElementById("total_belanja").value = totalBelanja;
      });
    }
  </script>

  <h2>List Transaksi</h2>

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
			<a href="update_transaksi.php">Edit | </a>
          <a href="transaksi.php?delete_transaksi=<?= $item['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
