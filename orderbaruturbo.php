<?php
// Database configuration (for local development, please update these as needed)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laundrygo";

// Create connection and create database if not exists
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Create table if not exists
$sql_create = "CREATE TABLE IF NOT EXISTS paket_turbo (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    nomor_telephone VARCHAR(20) NOT NULL,
    berat_kg DECIMAL(5,2) NOT NULL,
    tanggal_order DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    keterangan TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($sql_create);

// Initialize variables for feedback
$success_msg = "";
$error_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_order'])) {
    // Simple input sanitization
    $nama_pelanggan = trim($conn->real_escape_string($_POST['nama_pelanggan']));
    $nomor_telephone = trim($conn->real_escape_string($_POST['nomor_telephone']));
    $berat_kg = floatval($_POST['berat_kg']);
    $tanggal_order = $_POST['tanggal_order'];
    $tanggal_selesai = $_POST['tanggal_selesai'];

    $keterangan = trim($conn->real_escape_string($_POST['keterangan']));

    // Validate required fields
    if ($nama_pelanggan == "" || $nomor_telephone == "" || $berat_kg <= 0 || $tanggal_order == "" || $tanggal_selesai == "") {
        $error_msg = "Mohon isi semua bidang yang diperlukan dengan benar.";
    } else {
        // Insert into database
        // Ambil salah satu package ID dari tabel packages, misalnya default: Express
$default_package = $conn->query("SELECT id FROM packages WHERE name = 'Cuci Komplit Expres' LIMIT 1")->fetch_assoc();
$package_id = $default_package ? $default_package['id'] : 1; // fallback ke ID 1 jika tidak ditemukan

// Buat nomor order otomatis (misalnya CK-01, CK-02, dst)
$order_number = "CK-" . str_pad(rand(1, 999), 3, "0", STR_PAD_LEFT); // Bisa diganti sesuai format
$order_date = $tanggal_order;

// Simpan ke tabel `orders`
$sql_insert = "INSERT INTO orders (order_number, order_date, customer_name, package_id, weight_kg) 
VALUES ('$order_number', '$order_date', '$nama_pelanggan', $package_id, $berat_kg)";

if ($conn->query($sql_insert) === TRUE) {
    $success_msg = "Order berhasil disimpan dan ditampilkan di dashboard!";
    header("Location: dashboard.php"); // Langsung pindah ke dashboard setelah berhasil
    exit();
} else {
    $error_msg = "Terjadi kesalahan: " . $conn->error;
}


    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Laundry Go - Daftar Paket Turbo</title>
  <link rel="icon" type="image/png" href="asset/Group 3 - Copy.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="orderbaruturbo.css">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="modal.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main>
    <div class="container">
        <button aria-label="Kembali ke menu utama" onclick="goBack()" class="back-arrow" style="border:none; background:none;">
  <i class="fas fa-arrow-left"></i> <!-- Jika kamu pakai Font Awesome -->
</button>
    <h1>DAFTAR<i> PACKET TURBO</i></h1>

    <?php if($success_msg): ?>
      <div class="messages success" role="alert"><?=htmlspecialchars($success_msg)?></div>
    <?php endif; ?>
    <?php if($error_msg): ?>
      <div class="messages error" role="alert"><?=htmlspecialchars($error_msg)?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div>
        <label for="nama_pelanggan">Nama Pelanggan</label>
        <input type="text" id="nama_pelanggan" name="nama_pelanggan" required />
      </div>

      <div>
        <label for="berat_kg">Berat (kg)</label>
        <input type="number" id="berat_kg" name="berat_kg" min="0.01" step="0.01" placeholder="Berat (kg)" required />
      </div>

      <div>
        <label for="nomor_telephone">Nomor Telephone</label>
        <input type="text" id="nomor_telephone" name="nomor_telephone" required />
      </div>

      <div>
        <label for="tanggal_order">Tanggal Order</label>
        <input type="datetime-local" id="tanggal_order" name="tanggal_order" required />
      </div>
      
      <div class="form-full-width">
        <label for="keterangan">Keterangan</label>
        <textarea id="keterangan" name="keterangan" rows="3"></textarea>
      </div>

      <div>
        <label for="tanggal_selesai">Tanggal Selesai</label>
        <input type="datetime-local" id="tanggal_selesai" name="tanggal_selesai" required />
      </div>

      <div class="buttons">
        <button type="submit" id="pesan" name="submit_order" aria-label="Pesan Paket Turbo">Pesan</button>
        <button type="reset" id="batal" aria-label="Batalkan pengisian form">Batal</button>
      </div>
    </form>
    </div>
  </main>

  <script>
  function goBack() {
    window.location.href = 'daftarpaket2.php'; // Ganti dengan path sebenarnya jika berbeda
  }
</script>
</body>
</html>

<?php
$conn->close();
?>

