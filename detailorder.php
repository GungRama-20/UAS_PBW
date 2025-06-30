<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>LaundryGo - Detail Order</title>
  <link rel="icon" type="image/png" href="asset/Group 3 - Copy.png" />
  <link rel="stylesheet" href="detailorder.css">
  <link rel="stylesheet" href="header.css">
</head>
<body>

<?php
// Koneksi ke MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'laundrygo';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan kolom status ada (sekali jalan saja)

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    echo "<p>Order ID tidak ditemukan.</p>";
    exit;
}
$orderId = (int) $_GET['id'];

// Ambil detail order
$stmt = $conn->prepare("
    SELECT 
        o.id, o.order_number, o.order_date, o.customer_name, o.weight_kg, o.status,
        p.name AS nama_paket, p.duration AS durasi_pengerjaan, p.price AS harga_per_kg
    FROM orders o
    JOIN packages p ON o.package_id = p.id
    WHERE o.id = ?
");
$stmt->bind_param('i', $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "<p>Data order tidak ditemukan.</p>";
    exit;
}
?>

<?php include 'header.php'; ?>

<main role="main">
  <h1>Detail Order</h1>

  <table>
    <tr><td>No. Order</td><td><?= htmlspecialchars($order['order_number']) ?></td></tr>
    <tr><td>Tanggal Order</td><td><?= htmlspecialchars($order['order_date']) ?></td></tr>
    <tr><td>Nama Pelanggan</td><td><?= htmlspecialchars($order['customer_name']) ?></td></tr>
    <tr><td>Jenis Paket</td><td><?= htmlspecialchars($order['nama_paket']) ?></td></tr>
    <tr><td>Durasi Pengerjaan</td><td><?= htmlspecialchars($order['durasi_pengerjaan']) ?></td></tr>
    <tr><td>Berat</td><td><?= htmlspecialchars($order['weight_kg']) ?> kg</td></tr>
    <tr><td>Harga / Kg</td><td>Rp <?= number_format($order['harga_per_kg'], 0, ',', '.') ?></td></tr>
    <tr><td>Total</td><td>Rp <?= number_format($order['harga_per_kg'] * $order['weight_kg'], 0, ',', '.') ?></td></tr>
  </table>

  <hr>

  <?php if ($order['status'] === 'paid'): ?>
    <p style="color: green; font-weight: bold;">Status: Sudah Dibayar</p>
  <?php else: ?>
    <form method="GET" action="pembayaran.php">
      <input type="hidden" name="id" value="<?= $order['id'] ?>">
      <button type="submit" class="btn-pay">Bayar</button>
    </form>
  <?php endif; ?>
</main>

</body>
</html>
