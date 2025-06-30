<?php
// Konfigurasi database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'laundrygo';

// Koneksi database
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Membuat tabel jika belum ada
$conn->query(
    "CREATE TABLE IF NOT EXISTS paket (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        berat VARCHAR(50) NOT NULL,
        durasi VARCHAR(50) NOT NULL,
        tarif INT NOT NULL
    )"
);

// Proses tambah/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $berat = $_POST['berat'] ?? '';
    $durasi = $_POST['durasi'] ?? '';
    $tarif = $_POST['tarif'] ?? 0;

    if ($nama && $berat && $durasi && $tarif > 0) {
        if ($id) {
            $stmt = $conn->prepare("UPDATE paket SET nama=?, berat=?, durasi=?, tarif=? WHERE id=?");
            $stmt->bind_param("sssii", $nama, $berat, $durasi, $tarif, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO paket (nama, berat, durasi, tarif) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nama, $berat, $durasi, $tarif);
        }
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Proses delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $conn->query("DELETE FROM paket WHERE id=$del_id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Ambil data
$paket_list = [];
$result = $conn->query("SELECT * FROM paket ORDER BY id ASC");
while ($row = $result->fetch_assoc()) {
    $paket_list[] = $row;
}

// Mode edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM paket WHERE id=$edit_id");
    if ($res && $res->num_rows) {
        $edit_data = $res->fetch_assoc();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laundry Go - Paket Reguler</title>
  <link rel="icon" type="image/png" href="asset/Group 3 - Copy.png" />
    <link rel="stylesheet" href="packetturbo.css">
    <link rel="stylesheet" href="header.css">
    <style>
        /* Modal Styling */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 100;
        }
        .modal-backdrop.active {
            display: flex;
        }
        .modal-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        .modal-box h3 {
            margin-top: 0;
            color: #333;
        }
        .modal-buttons {
            margin-top: 20px;
        }
        .modal-buttons button,
        .modal-buttons a {
            padding: 8px 16px;
            margin: 0 8px;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        .btn-cancel {
            background-color: #ccc;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <h1>PAKET REGULER</h1>
    <button class="btn-add" onclick="scrollToForm()">+Tambah Paket</button>
    <div class="clearfix"></div>

    <section>
        <h2>DAFTAR ORDERAN</h2>
        <hr />
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA</th>
                    <th>BERAT(KG)</th>
                    <th>DURASI</th>
                    <th>TARIF</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($paket_list): ?>
                <?php foreach ($paket_list as $index => $paket): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($paket['nama']) ?></td>
                    <td><?= htmlspecialchars($paket['berat']) ?></td>
                    <td><?= htmlspecialchars($paket['durasi']) ?></td>
                    <td><?= number_format($paket['tarif'], 0, ',', '.') ?></td>
                    <td>
                        <a class="action-btn btn-edit" href="?edit=<?= $paket['id'] ?>" aria-label="Edit paket <?= htmlspecialchars($paket['nama']) ?>">Edit</a>
                        <a class="action-btn btn-delete" href="#" onclick="showModal('<?= $paket['id'] ?>', '<?= htmlspecialchars($paket['nama']) ?>'); return false;" aria-label="Hapus paket <?= htmlspecialchars($paket['nama']) ?>">Hapus</a>
                    </td>

                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">Belum ada data</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section id="form-section">
        <form method="post">
            <h2><?= $edit_data ? "Edit Paket" : "Tambah Paket Baru" ?></h2>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
            <label>Nama Paket</label>
            <input type="text" name="nama" required value="<?= $edit_data['nama'] ?? '' ?>">
            <label>Berat (kg)</label>
            <input type="text" name="berat" required value="<?= $edit_data['berat'] ?? '' ?>">
            <label>Durasi</label>
            <input type="text" name="durasi" required value="<?= $edit_data['durasi'] ?? '' ?>">
            <label>Tarif (Rp)</label>
            <input type="number" name="tarif" required value="<?= $edit_data['tarif'] ?? '' ?>">
            <button type="submit"><?= $edit_data ? "Update" : "Tambah" ?></button>
        </form>
    </section>
</main>

<!-- Modal Konfirmasi -->
<div id="deleteModal" class="modal-backdrop">
    <div class="modal-box">
        <h3>Hapus Paket</h3>
        <p>Apakah kamu yakin ingin menghapus <strong id="paketNama"></strong>?</p>
        <div class="modal-buttons">
            <button class="btn-cancel" onclick="closeModal()">Batal</button>
            <a id="confirmDeleteBtn" class="btn-delete" href="#">Ya, Hapus</a>
        </div>
    </div>
</div>

<script>
function scrollToForm() {
    document.getElementById('form-section').scrollIntoView({ behavior: 'smooth' });
}

function showModal(id, nama) {
    document.getElementById('paketNama').textContent = nama;
    document.getElementById('confirmDeleteBtn').href = '?delete=' + id;
    document.getElementById('deleteModal').classList.add('active');
}

function closeModal() {
    document.getElementById('deleteModal').classList.remove('active');
}
</script>
</body>
</html>
