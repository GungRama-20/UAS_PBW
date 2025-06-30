<?php
// Database connection (SQLite for simplicity, no separate DB server needed)
$db = new PDO('sqlite:laundrygo.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create tables if not exist
$db->exec("
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_no TEXT UNIQUE NOT NULL,
    nominal INTEGER NOT NULL,
    paid INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    position TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS packages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    price INTEGER NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
");

// Helper function to generate order number
function generateOrderNo() {
    return 'Go-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

// On form submit transaction
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nominal'])) {
    $nominal = intval($_POST['nominal']);
    $order_no = $_POST['order_no'];
    if ($nominal >= 10000) {
        // Insert or update transaction
        $stmt = $db->prepare("INSERT OR IGNORE INTO transactions (order_no, nominal) VALUES (:order_no, :nominal)");
        $stmt->execute([':order_no' => $order_no, ':nominal' => $nominal]);

        $stmt = $db->prepare("UPDATE transactions SET nominal = :nominal, paid = 1 WHERE order_no = :order_no");
        $stmt->execute([':nominal' => $nominal, ':order_no' => $order_no]);

        $message = "Transaksi berhasil, nominal: Rp " . number_format($nominal,0,",",".");
    } else {
        $message = "Nominal harus minimal Rp 10.000";
    }
}

// Generate a new order number or get from post (hidden field)
$order_no = $_POST['order_no'] ?? generateOrderNo();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Laundry Go - Transaksi</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="pembayaran.css">
<link rel="stylesheet" href="header.css">
<link rel="stylesheet" href="modal.css">
</head>
<body class="min-h-screen flex flex-col">
<?php include 'header.php'; ?>

<main class="flex-grow flex justify-center items-center p-6">
  <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full flex flex-col items-center">
    <div class="mb-6">
      <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/5b3bcdd3-bc52-4012-a557-ed16de35b6fc.png" alt="Icon of a hand holding a smartphone showing a dollar sign and wireless payment signal lines in blue and black outlines on white background" class="w-24 h-24 mx-auto" onerror="this.src='https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/d8d77b5a-5a43-4191-87ab-50a52d2f5101.png'"/>
    </div>
    <div class="text-center mb-4">
      <strong class="text-xl block mb-1">#no_order: <span id="orderNo"><?php echo htmlspecialchars($order_no); ?></span></strong>
      <p class="text-gray-500 text-sm">Masukkan jumlah nominal untuk melakukan transaksi</p>
    </div>

    <?php if ($message): ?>
      <div class="mb-4 px-4 py-3 text-center rounded bg-green-100 text-green-800 w-full"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" class="w-full" onsubmit="return validateNominal()">
      <input type="hidden" name="order_no" value="<?php echo htmlspecialchars($order_no); ?>" />
      <label for="nominal" class="text-gray-400 font-bold mb-1 block">Nominal : </label>
      <input 
        type="number" 
        id="nominal" 
        name="nominal" 
        min="10000" 
        step="1000"
        value="10000" 
        class="w-full py-3 px-4 border border-gray-300 rounded mb-6 focus:outline-none focus:ring-2 focus:ring-blue-400"
        required />
      <button 
        type="submit" 
        class="bg-gradient-to-r from-blue-400 to-blue-700 text-white font-semibold w-full py-3 rounded hover:from-blue-500 hover:to-blue-800 transition-colors"
      >Bayar</button>
    </form>
  </div>
  <!-- Modal Pembayaran Berhasil -->
<div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
  <div class="bg-white rounded-lg p-6 text-center max-w-sm w-full shadow-lg">
    <div class="mx-auto mb-4">
      <img src="asset/Vector.png" alt="Pembayaran Berhasil" class="w-24 h-24 mx-auto">
    </div>
    <h2 class="text-xl font-semibold mb-2">Pembayaran Berhasil</h2>
    <button 
      onclick="closeModal()" 
      class="bg-gradient-to-r from-blue-400 to-blue-700 text-white px-6 py-2 rounded hover:from-blue-500 hover:to-blue-800 transition"
    >
      Ok
    </button>
  </div>
</div>

</main>
<script>
  function closeModal() {
    document.getElementById("successModal").classList.add("hidden");
    window.location.href = "dashboard.php"; // redirect setelah klik OK
  }

  // Jika pesan sukses ada, tampilkan modal
  <?php if ($message && strpos($message, 'Transaksi berhasil') !== false): ?>
    window.addEventListener('DOMContentLoaded', () => {
      document.getElementById("successModal").classList.remove("hidden");
    });
  <?php endif; ?>
</script>

<script>
  function validateNominal() {
    const input = document.getElementById('nominal');
    const val = parseInt(input.value, 10);
    if (isNaN(val) || val < 10000) {
      alert('Nominal harus minimal Rp 10.000');
      input.focus();
      return false;
    }
    return true;
  }
</script>
</body>
</html>

