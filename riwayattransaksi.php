<?php
// Simple Laundry Go system with database creation, CSS, HTML, and PHP in one file

// Database connection and setup (SQLite for simplicity, so no external DB setup)
// You can change to MySQL or other by editing the connection and table setup accordingly
$db = new PDO('sqlite:laundrygo.sqlite3');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create table if not exists (only if first run)
$db->exec("
CREATE TABLE IF NOT EXISTS transaksi (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    no_order TEXT NOT NULL,
    nama TEXT NOT NULL,
    jenis_paket TEXT NOT NULL,
    jumlah TEXT NOT NULL,
    total TEXT NOT NULL,
    nominal_bayar TEXT NOT NULL,
    kembalian TEXT NOT NULL,
    status TEXT NOT NULL
);
");

// Insert sample data if table empty (run only once)
$count = $db->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
if ($count == 0) {
    $sampleData = [
        ['Go-784383', 'Joko', 'Packet Turbo', '5 kg', 'Rp. 50.000', 'Rp. 50.000', 'Rp. 0', 'Sukses'],
        ['Go-784384', 'Juki', 'Packet Turbo', '5 kg', 'Rp. 50.000', 'Rp. 50.000', 'Rp. 0', 'Sukses'],
        ['Go-784385', 'Jeko', 'Packet Turbo', '5 kg', 'Rp. 50.000', 'Rp. 50.000', 'Rp. 0', 'Sukses'],
        ['Go-784386', 'Jeki', 'Packet Turbo', '5 kg', 'Rp. 50.000', 'Rp. 50.000', 'Rp. 0', 'Sukses'],
        ['Go-784387', 'Jaki', 'Packet Turbo', '5 kg', 'Rp. 50.000', 'Rp. 50.000', 'Rp. 0', 'Sukses'],
    ];

    $stmt = $db->prepare("INSERT INTO transaksi (no_order, nama, jenis_paket, jumlah, total, nominal_bayar, kembalian, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach($sampleData as $row) {
        $stmt->execute($row);
    }
}

// Fetch all transactions
$transactions = $db->query("SELECT * FROM transaksi")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Laundry Go - Riwayat Transaksi</title>
  <link rel="icon" type="image/png" href="asset/Group 3 - Copy.png" />
  <link rel="stylesheet" href="riwayattransaksi.css">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="modal.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main role="main" aria-labelledby="main-title">
    <h1 id="main-title">RIWAYAT TRANSAKSI</h1>

    <section aria-labelledby="section-title-transaksi">
      <h2 id="section-title-transaksi">DAFTAR TRANSAKSI</h2>
      <table role="table" aria-describedby="transaksi-desc">
        <caption id="transaksi-desc" class="sr-only">
          Daftar rincian transaksi laundry yang sudah dilakukan
        </caption>
        <thead>
          <tr>
            <th scope="col">NO</th>
            <th scope="col">NO. ORDER</th>
            <th scope="col">NAMA</th>
            <th scope="col">JENIS PAKET</th>
            <th scope="col">JUMLAH</th>
            <th scope="col">TOTAL</th>
            <th scope="col">NOMINAL BAYAR</th>
            <th scope="col">KEMBALIAN</th>
            <th scope="col">STATUS</th>
            <th scope="col">ACTION</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          foreach ($transactions as $row) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . htmlspecialchars($row['no_order']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
            echo "<td>" . htmlspecialchars($row['jenis_paket']) . "</td>";
            echo "<td>" . htmlspecialchars($row['jumlah']) . "</td>";
            echo "<td>" . htmlspecialchars($row['total']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nominal_bayar']) . "</td>";
            echo "<td>" . htmlspecialchars($row['kembalian']) . "</td>";
            echo "<td class='status-sukses'>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td class='action-buttons'>";
            echo "<button class='btn-detail' aria-label='Detail transaksi nomor order " . htmlspecialchars($row['no_order']) . "' onclick='showDetail(" . $row['id'] . ")'>Detail</button>";
            echo "<button class='btn-cetak' aria-label='Cetak bukti transaksi nomor order " . htmlspecialchars($row['no_order']) . "' onclick='printReceipt(" . $row['id'] . ")'>Cetak Bukti</button>";
            echo "</td>";
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
  </main>

  <!-- Detail overlay -->
  <div class="overlay" id="detailOverlay" role="dialog" aria-modal="true" aria-labelledby="detailTitle" aria-describedby="detailDesc">
    <div class="detail-card" tabindex="0">
      <h3 id="detailTitle" class="detail-header">Detail Transaksi</h3>
      <div id="detailDesc" class="detail-content">
        <p><strong>Memuat data...</strong></p>
      </div>
      <button class="close-btn" aria-label="Tutup detail transaksi" onclick="closeDetail()">Tutup</button>
    </div>
  </div>

  <script>
    const transactions = <?php echo json_encode($transactions); ?>;
    const overlay = document.getElementById('detailOverlay');
    const detailContent = document.getElementById('detailDesc');

    function showDetail(id) {
      const trx = transactions.find(item => item.id == id);
      if (!trx) {
        detailContent.innerHTML = "<p>Data tidak ditemukan.</p>";
      } else {
        let html = '';
        html += detailRow('No. Order', trx.no_order);
        html += detailRow('Nama', trx.nama);
        html += detailRow('Jenis Paket', trx.jenis_paket);
        html += detailRow('Jumlah', trx.jumlah);
        html += detailRow('Total', trx.total);
        html += detailRow('Nominal Bayar', trx.nominal_bayar);
        html += detailRow('Kembalian', trx.kembalian);
        html += detailRow('Status', trx.status);
        detailContent.innerHTML = html;
      }
      overlay.classList.add('active');
      // Focus for accessibility
      overlay.querySelector('.detail-card').focus();
    }

    function closeDetail() {
      overlay.classList.remove('active');
    }

    // Utility for detail row
    function detailRow(label, value) {
      return '<div class="detail-row"><span class="detail-label">' + label + ':</span><span>' + escapeHtml(value) + '</span></div>';
    }

    // Basic HTML escaping for JS
    function escapeHtml(text) {
      return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    // "Print" placeholder function
   function printReceipt(id) {
  const trx = transactions.find(item => item.id == id);
  if (!trx) {
    alert('Data transaksi tidak ditemukan.');
    return;
  }

  const receiptHTML = `
    <html>
    <head>
      <title>Bukti Transaksi</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          padding: 20px;
          width: 600px;
          margin: auto;
        }
          .logo {
          text-align: center;
          margin-bottom: 10px;
        }

        .logo-img {
          max-width: 200px;
          width: 100%;
          height: auto;
          object-fit: contain;
        }

        .title {
          text-align: center;
          margin-top: 5px;
          font-size: 18px;
          font-weight: bold;
        }
        .invoice-number {
          text-align: center;
          margin-bottom: 20px;
        }
        .section {
          margin-bottom: 15px;
        }
        .section strong {
          display: inline-block;
          width: 130px;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 10px;
        }
        table, th, td {
          border: 1px solid #999;
        }
        th, td {
          padding: 8px;
          text-align: left;
        }
        .total-section td {
          border: none;
        }
        .footer {
          text-align: center;
          margin-top: 30px;
        }
      </style>
    </head>
    <body>
      <div class="logo">
        <img src="asset/Group 3.png" alt="Logo Laundry" class="logo-img" />
      </div>
      <div class="invoice-number">Invoice number: ${trx.no_order}</div>

      <div class="section">
        <div><strong>Nama pelanggan</strong> ${trx.nama}</div>
        <div><strong>Nomor telepon</strong> 085555000555</div>
        <div><strong>Alamat</strong> Jl. Melati Medan</div>
        <div><strong>Tanggal order</strong> 30 Juni 2025</div>
        <div><strong>Diambil pada</strong> 30 Juni 2025</div>
      </div>

      <table>
        <thead>
          <tr>
            <th>Jenis paket</th>
            <th>Berat (Kg)</th>
            <th>Harga Per-kilo</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>${trx.jenis_paket}</td>
            <td>${trx.jumlah}</td>
            <td>Rp. ${(parseInt(trx.total.replace(/\D/g,'')) / parseInt(trx.jumlah)).toLocaleString()}</td>
          </tr>
        </tbody>
      </table>

      <table class="total-section">
        <tr>
          <td><strong>Total</strong></td>
          <td> ${trx.total}</td>
        </tr>
        <tr>
          <td><strong>Nominal Bayar</strong></td>
          <td>${trx.nominal_bayar}</td>
        </tr>
        <tr>
          <td><strong>Uang kembali</strong></td>
          <td>${trx.kembalian}</td>
        </tr>
      </table>

      <div class="footer">
        <p>LAUNDRY GO</p>
        <p>Terima kasih telah menggunakan jasa kami.</p>
      </div>
    </body>
    </html>
  `;

  const printWindow = window.open('', '_blank', 'width=800,height=900');
  printWindow.document.open();
  printWindow.document.write(receiptHTML);
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
}


    // Close overlay on outside click or Escape key
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) {
        closeDetail();
      }
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === "Escape" && overlay.classList.contains('active')) {
        closeDetail();
      }
    });
  </script>

  <style>
    /* Screen reader only utility */
    .sr-only {
      position: absolute !important;
      width: 1px !important;
      height: 1px !important;
      padding: 0 !important;
      margin: -1px !important;
      overflow: hidden !important;
      clip: rect(0, 0, 0, 0) !important;
      border: 0 !important;
    }
  </style>
</body>
</html>

