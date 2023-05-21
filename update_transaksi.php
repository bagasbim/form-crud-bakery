<?php
include 'koneksi.php';

// Mendapatkan data transaksi dari database
$query_transaksi = "SELECT transaksi.id, customer.nama_customer, transaksi.tanggal_transaksi, SUM(detail_transaksi.qty * detail_transaksi.harga) AS total_belanja FROM transaksi LEFT JOIN customer ON transaksi.customer_id = customer.id LEFT JOIN detail_transaksi ON transaksi.id = detail_transaksi.transaksi_id GROUP BY transaksi.id";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);
$transaksi = mysqli_fetch_all($result_transaksi, MYSQLI_ASSOC);

// Mendapatkan data customer dari database
$query_customer = "SELECT * FROM customer";
$result_customer = mysqli_query($koneksi, $query_customer);
$customer = mysqli_fetch_all($result_customer, MYSQLI_ASSOC);

// Mendapatkan data kue dari database
$query_kue = "SELECT * FROM kue";
$result_kue = mysqli_query($koneksi, $query_kue);
$kue = mysqli_fetch_all($result_kue, MYSQLI_ASSOC);

// Fungsi untuk mengupdate transaksi
if (isset($_POST['update_transaksi'])) {
  $transaksi_id = $_POST['transaksi_id'];
  $customer_id = $_POST['customer_id'];
  $tanggal_transaksi = $_POST['tanggal_transaksi'];

  // Mendapatkan total belanja dari form
  $total_belanja = $_POST['total_belanja'];

  $query_update = "UPDATE transaksi SET customer_id = '$customer_id', tanggal_transaksi = '$tanggal_transaksi' WHERE id = $transaksi_id";
  mysqli_query($koneksi, $query_update);

  // Hapus detail transaksi sebelumnya
  $query_delete_detail = "DELETE FROM detail_transaksi WHERE transaksi_id = $transaksi_id";
  mysqli_query($koneksi, $query_delete_detail);

  // Simpan detail transaksi yang diupdate
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
?>

<!DOCTYPE html>
<html>
<head>
  <title>Transaksi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Transaksi</h1>

  <form action="" method="POST">
    <?php if (isset($_GET['edit_transaksi'])):
      $transaksi_id = $_GET['edit_transaksi'];
      $query_get_transaksi = "SELECT * FROM transaksi WHERE id = $transaksi_id";
      $result_get_transaksi = mysqli_query($koneksi, $query_get_transaksi);
      $data_transaksi = mysqli_fetch_assoc($result_get_transaksi);
      ?>
      <input type="hidden" name="transaksi_id" value="<?= $data_transaksi['id']; ?>">
      <label for="customer_id">Customer:</label>
      <select name="customer_id" id="customer_id">
        <option value="0" <?= ($data_transaksi['customer_id'] == 0) ? 'selected' : ''; ?>>Walk In Customer</option>
        <?php foreach ($customer as $item): ?>
          <option value="<?= $item['id']; ?>" <?= ($data_transaksi['customer_id'] == $item['id']) ? 'selected' : ''; ?>><?= $item['nama_customer']; ?></option>
        <?php endforeach; ?>
      </select>
      <br>
      <label for="tanggal_transaksi">Tanggal Transaksi:</label>
      <input type="date" name="tanggal_transaksi" id="tanggal_transaksi" value="<?= $data_transaksi['tanggal_transaksi']; ?>" required>
      <br><br>
      <table>
        <tr>
          <th>Kue</th>
          <th>Qty</th>
          <th>Harga</th>
          <th>Total</th>
        </tr>
        <?php
        $query_get_detail = "SELECT * FROM detail_transaksi WHERE transaksi_id = $transaksi_id";
        $result_get_detail = mysqli_query($koneksi, $query_get_detail);
        $data_detail = mysqli_fetch_all($result_get_detail, MYSQLI_ASSOC);

        foreach ($data_detail as $index => $item):
          $kue_id = $item['kue_id'];
          $query_get_kue = "SELECT * FROM kue WHERE id = $kue_id";
          $result_get_kue = mysqli_query($koneksi, $query_get_kue);
          $data_kue = mysqli_fetch_assoc($result_get_kue);
          ?>
          <tr>
            <td><?= $data_kue['nama_kue']; ?></td>
            <td><input type="number" name="qty[]" min="1" value="<?= $item['qty']; ?>" required></td>
            <td><?= $item['harga']; ?></td>
            <td id="total"><?= $item['qty'] * $item['harga']; ?></td>
            <input type="hidden" name="kue_id[]" value="<?= $kue_id; ?>">
            <input type="hidden" name="harga[]" value="<?= $item['harga']; ?>">
          </tr>
        <?php endforeach; ?>
      </table>
      <br>
      <label for="total_belanja">Total Belanja:</label>
      <input type="text" name="total_belanja" id="total_belanja" value="<?= $data_transaksi['total_belanja']; ?>" readonly>
      <br><br>
      <button type="submit" name="update_transaksi">Update</button>
    <?php endif; ?>
  </form>

  <h2>List Transaksi</h2>

  <table>
    <tr>
      <th>ID</th>
      <th>Nama Customer</th>
      <th>Tanggal Transaksi</th>
      <th>Total Belanja</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($transaksi as $item): ?>
      <tr>
        <td><?= $item['id']; ?></td>
        <td><?= $item['nama_customer']; ?></td>
        <td><?= $item['tanggal_transaksi']; ?></td>
        <td><?= $item['total_belanja']; ?></td>
        <td>
          <a href="transaksi.php?edit_transaksi=<?= $item['id']; ?>">Edit | </a>
          <a href="transaksi.php?delete_transaksi=<?= $item['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>

  <script>
    // Menghitung total belanja saat mengubah nilai qty
    var qtyInputs = document.getElementsByName('qty[]');
    var hargaInputs = document.getElementsByName('harga[]');
    var totalBelanjaInput = document.getElementById('total_belanja');

    Array.from(qtyInputs).forEach(function (input, index) {
      input.addEventListener('input', function () {
        var qty = parseInt(input.value);
        var harga = parseInt(hargaInputs[index].value);
        var total = qty * harga;

        document.getElementById('total').innerText = total;
        hitungTotalBelanja();
      });
    });

    function hitungTotalBelanja() {
      var totals = Array.from(document.querySelectorAll('#total'));
      var totalBelanja = 0;

      totals.forEach(function (total) {
        totalBelanja += parseInt(total.innerText);
      });

      totalBelanjaInput.value = totalBelanja;
    }
  </script>
</body>
</html>
