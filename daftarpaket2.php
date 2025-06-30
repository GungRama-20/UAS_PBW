<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laundrygo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed to database: " . $conn->connect_error);
}

$sql = "SELECT id, package_name, description, image_url FROM package_list";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Laundry Go - Daftar Paket</title>
  <link rel="icon" type="image/png" href="asset/Group 3 - Copy.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="daftarpaket.css">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="modal.css">
  <script>
    function goBack() {
      alert("Navigasi kembali ke halaman sebelumnya atau menu utama");
    }
  </script>
</head>
<body>
<?php include 'header.php'; ?>

<main role="main" aria-labelledby="paket-title" style="position:relative;">
  <h1 id="paket-title">DAFTAR PACKET YANG TERSEDIA</h1>
  <button aria-label="Kembali ke menu utama" onclick="goBack()" class="back-arrow" style="border:none; background:none;">
  <i class="fas fa-arrow-left"></i> <!-- Jika kamu pakai Font Awesome -->
</button>


  <section class="packages-grid" aria-live="polite">
    <?php if ($result && $result->num_rows > 0): ?>
     <?php
$shown = [];
while($row = $result->fetch_assoc()):
  if (in_array($row['package_name'], $shown)) continue;
  $shown[] = $row['package_name'];
?>
  <?php
  $targetPage = '#'; // default jika tidak dikenali
  if ($row['package_name'] === 'Packet Turbo') {
    $targetPage = 'orderbaruturbo.php';
  } elseif ($row['package_name'] === 'Paket Reguler') {
    $targetPage = 'orderbarureguler.php';
  }
?>

<a href="<?= $targetPage ?>" style="text-decoration: none; color: inherit;">
  <article class="package-card">
    <div class="package-image">
      <?php
        $imgSrc = '';
        if ($row['package_name'] === 'Packet Turbo') {
          $imgSrc = 'asset/image 4.png';
        } elseif ($row['package_name'] === 'Paket Reguler') {
          $imgSrc = 'asset/image (3).png';
        } else {
          $imgSrc = 'asset/default.png';
        }
      ?>
      <img src="<?= $imgSrc ?>" alt="Paket <?= htmlspecialchars($row['package_name']) ?>">
    </div>
    <div class="package-name">
      <strong>
        <?php if ($row['package_name'] === 'Packet Turbo'): ?>
          <em><?= htmlspecialchars($row['package_name']) ?></em>
        <?php else: ?>
          <?= htmlspecialchars($row['package_name']) ?>
        <?php endif; ?>
      </strong>
    </div>
  </article>
</a>

<?php endwhile; ?>


    <?php else: ?>
      <p>Tidak ada paket laundry yang tersedia saat ini.</p>
    <?php endif; ?>
  </section>
  <script>
  function goBack() {
    window.location.href = 'dashboard.php'; // Ganti dengan path sebenarnya jika berbeda
  }
</script>
</main>
</body>
</html>

<?php $conn->close(); ?>
