<?php
// Database Configuration - edit these for your environment
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'laundrygo';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
$conn->select_db($db_name);

// Create tables if not exists (basic schema)
/*
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL,
    order_date DATE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    package_id INT NOT NULL,
    weight_kg DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
);
*/

// Ensure employees table
$conn->query("CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)");

// Ensure packages table
$conn->query("CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL
)");

// Ensure orders table
$conn->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL,
    order_date DATE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    package_id INT NOT NULL,
    weight_kg DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
)");

// Seed initial data if empty

// Employees seed
$res = $conn->query("SELECT COUNT(*) AS cnt FROM employees");
$row = $res->fetch_assoc();
if ($row['cnt'] == 0) {
    $conn->query("INSERT INTO employees (name) VALUES 
        ('Nyoman Joko'), ('Made Ngurah'), ('Wayan Suplag'), ('Gung Suju'), ('Gede Sengker')");
}

// Packages seed
$res = $conn->query("SELECT COUNT(*) AS cnt FROM packages");
$row = $res->fetch_assoc();
if ($row['cnt'] == 0) {
    $conn->query("INSERT INTO packages (name, duration, price) VALUES 
        ('Cuci Komplit Expres', '3 Jam', 25000.00),
        ('Cuci Komplit Reguler', '2 Hari', 15000.00),
        ('Cuci Komplit Reguler', '3 Hari', 13000.00)
    ");
}

// Orders seed
$res = $conn->query("SELECT COUNT(*) AS cnt FROM orders");
$row = $res->fetch_assoc();
if ($row['cnt'] == 0) {
    // Retrieve package IDs to use in orders
    $pkgExpres = $conn->query("SELECT id FROM packages WHERE name='Cuci Komplit Expres' LIMIT 1")->fetch_assoc()['id'];
    $pkgReguler = $conn->query("SELECT id FROM packages WHERE name='Cuci Komplit Reguler' ORDER BY duration ASC LIMIT 1")->fetch_assoc()['id'];
    $pkgReguler3 = $conn->query("SELECT id FROM packages WHERE name='Cuci Komplit Reguler' AND duration='3 Hari' LIMIT 1")->fetch_assoc()['id'];

    $conn->query("INSERT INTO orders (order_number, order_date, customer_name, package_id, weight_kg) VALUES
        ('CK-01', CURDATE(), 'Nyoman Joko', $pkgExpres, 4.0),
        ('CK-02', CURDATE(), 'Made Ngurah', $pkgReguler, 4.0),
        ('CK-03', CURDATE(), 'Wayan Suplag', $pkgExpres, 4.0),
        ('CK-04', CURDATE(), 'Gung Suju', $pkgExpres, 4.0),
        ('CK-05', CURDATE(), 'Gede Sengker', $pkgReguler3, 4.0)
    ");
}

// Handle Delete order action
if (isset($_GET['delete_order'])) {
    $delete_id = intval($_GET['delete_order']);
    $conn->query("DELETE FROM orders WHERE id = $delete_id");
    header("Location: " . strtok($_SERVER["REQUEST_URI"],'?'));
    exit();
}

// Handle order detail ajax request (if any)
if (isset($_GET['detail_order'])) {
    $detail_id = intval($_GET['detail_order']);
    $stmt = $conn->prepare("SELECT o.id,o.order_number,o.order_date,o.customer_name,p.name AS package_name,p.duration,o.weight_kg 
                            FROM orders o JOIN packages p ON o.package_id = p.id WHERE o.id = ?");
    $stmt->bind_param('i', $detail_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $detail = $result->fetch_assoc();
    header('Content-Type: application/json');
   echo json_encode($detail ? $detail : ['error' => 'Order not found']);
    exit();
}
// Fetch totals for dashboard
$tot_employees = $conn->query("SELECT COUNT(*) as cnt FROM employees")->fetch_assoc()['cnt'];
$tot_orders = $conn->query("SELECT COUNT(*) as cnt FROM orders")->fetch_assoc()['cnt'];
$tot_packages = $conn->query("SELECT COUNT(*) as cnt FROM packages")->fetch_assoc()['cnt'];

// Fetch order list with package info
$sql_orders = "SELECT o.id,o.order_number,o.order_date,o.customer_name,p.name AS package_name,p.duration,o.weight_kg 
               FROM orders o JOIN packages p ON o.package_id = p.id ORDER BY o.id ASC";
$res_orders = $conn->query($sql_orders);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Laundry Go - Dashboard</title>
  <link rel="icon" type="image/png" href="asset/Group 3 - Copy.png" />
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="header.css">
<link rel="stylesheet" href="modal.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container" role="main">
  <p class="welcome">Selamat Datang <strong>Admin</strong></p>
  <h1>DASHBOARD</h1>

  <section class="stats" aria-label="Dashboard statistics summary">
    <div class="card" role="region" aria-labelledby="employees-label">
      <div>
        <div id="employees-label" class="text">Jumlah karyawan</div>
        <div class="value" aria-live="polite" aria-atomic="true"><?= $tot_employees ?></div>
      </div>
      <img class="icon" src="asset/IMG_4734-removebg-preview.png" alt="Icon representing employees group" onerror="this.style.display='none'" />
    </div>
    <div class="card" role="region" aria-labelledby="orders-label">
      <div>
        <div id="orders-label" class="text">Total Order</div>
        <div class="value" aria-live="polite" aria-atomic="true"><?= $tot_orders ?></div>
      </div>
      <img class="icon" src="asset/IMG_4735-removebg-preview.png" alt="Icon representing money and transactions" onerror="this.style.display='none'" />
    </div>
    <div class="card" role="region" aria-labelledby="packages-label">
      <div>
        <div id="packages-label" class="text">Jumlah Paket Tersedia</div>
        <div class="value" aria-live="polite" aria-atomic="true"><?= $tot_packages ?></div>
      </div>
      <img class="icon" src="asset/IMG_4736-removebg-preview.png" alt="Icon representing packages available on smartphone" onerror="this.style.display='none'" />
    </div>
  </section>

  <section aria-labelledby="orders-title">
    <div class="orders-header">
      <h2 id="orders-title" style="font-weight: 700; font-size: 1.4rem;">Order Cuci Go</h2>
      <button class="btn-primary" onclick="window.location.href='daftarpaket2.php'" aria-label="Add new order">+ Order Baru</button>
    </div>
    <table role="table" aria-describedby="orders-title">
      <thead>
        <tr>
          <th scope="col">NO</th>
          <th scope="col">NO. ORDER</th>
          <th scope="col">TGL ORDER</th>
          <th scope="col">NAMA PELANGGAN</th>
          <th scope="col">JENIS PAKET</th>
          <th scope="col">WAKTU KERJA</th>
          <th scope="col">BERAT(Kg)</th>
          <th scope="col">ACTION</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while($order = $res_orders->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($no++) ?></td>
          <td><?= htmlspecialchars($order['order_number']) ?></td>
          <td><?= htmlspecialchars($order['order_date']) ?></td>
          <td><?= htmlspecialchars($order['customer_name']) ?></td>
          <td><?= htmlspecialchars($order['package_name']) ?></td>
          <td><?= htmlspecialchars($order['duration']) ?></td>
          <td><?= htmlspecialchars($order['weight_kg']) ?> kg</td>
          <td>
            <a href="detailorder.php?id=<?= $order['id'] ?>" class="btn btn-detail" role="button" aria-label="Detail order <?= htmlspecialchars($order['order_number']) ?>">Detail</a>
            <a href="?delete_order=<?= $order['id'] ?>" class="btn btn-delete" role="button" aria-label="Delete order <?= htmlspecialchars($order['order_number']) ?>" onclick="return confirm('Hapus order <?= htmlspecialchars($order['order_number']) ?>?');">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>
</main>

<!-- Modal for detail -->
<div id="modal-backdrop" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-title" tabindex="-1">
  <div class="modal" role="document">
    <button class="close-btn" aria-label="Close detail modal" id="modal-close">&times;</button>
    <h2 id="modal-title">Detail Order</h2>
    <div class="modal-content">
      <p><span class="modal-label">No. Order:</span> <span id="modal-order-number"></span></p>
      <p><span class="modal-label">Tanggal Order:</span> <span id="modal-order-date"></span></p>
      <p><span class="modal-label">Nama Pelanggan:</span> <span id="modal-customer-name"></span></p>
      <p><span class="modal-label">Jenis Paket:</span> <span id="modal-package-name"></span></p>
      <p><span class="modal-label">Waktu Kerja:</span> <span id="modal-package-duration"></span></p>
      <p><span class="modal-label">Berat (Kg):</span> <span id="modal-weight"></span></p>
    </div>
  </div>
</div>

<script>
  (() => {
    const modal = document.getElementById('modal-backdrop');
    const modalClose = document.getElementById('modal-close');

    // Elements to fill modal detail
    const mdOrderNum = document.getElementById('modal-order-number');
    const mdOrderDate = document.getElementById('modal-order-date');
    const mdCustName = document.getElementById('modal-customer-name');
    const mdPkgName = document.getElementById('modal-package-name');
    const mdPkgDur = document.getElementById('modal-package-duration');
    const mdWeight = document.getElementById('modal-weight');

    function openModal(detail) {
      mdOrderNum.textContent = detail.order_number || '-';
      mdOrderDate.textContent = detail.order_date || '-';
      mdCustName.textContent = detail.customer_name || '-';
      mdPkgName.textContent = detail.package_name || '-';
      mdPkgDur.textContent = detail.duration || '-';
      mdWeight.textContent = detail.weight_kg ? detail.weight_kg + ' kg' : '-';
      modal.classList.add('active');
      modal.focus();
    }

    function closeModal() {
      modal.classList.remove('active');
    }

    // Handle close button
    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', e => {
      if(e.target === modal) closeModal();
    });
    document.addEventListener('keydown', e => {
      if (e.key === "Escape" && modal.classList.contains('active')) {
        closeModal();
      }
    });

    // Attach event listener to all detail buttons
    document.querySelectorAll('.btn-detail').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        fetch('?detail_order=' + id)
          .then(response => response.json())
          .then(data => {
            if(data.error) {
              alert(data.error);
            } else {
              openModal(data);
            }
          })
          .catch(() => alert("Gagal mengambil detail order."));
      });
    });

  })();
</script>
</body>
</html>

