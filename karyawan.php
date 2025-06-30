<?php
// --- DATABASE CONFIGURATION ---
// Change these values to match your database credentials
$host = "localhost";
$user = "root";
$password = "";
$dbname = "laundrygo";

// Create connection
$conn = mysqli_connect($host, $user, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if not exists
$sql_db = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
mysqli_query($conn, $sql_db);

// Select database
mysqli_select_db($conn, $dbname);

// Create table if not exists
$sql_table = "CREATE TABLE IF NOT EXISTS karyawan (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $sql_table);

// --- HANDLE FORM SUBMISSIONS ---
$errors = [];
$success_msg = "";

// Create or Update employee
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($nama)) {
        $errors[] = "Nama Karyawan wajib diisi.";
    }
    if (empty($username)) {
        $errors[] = "Username wajib diisi.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }

    $id = $_POST['id'] ?? '';

    if (empty($errors)) {
        if ($id) {
            // Update existing
            // Check for unique username/email except this id
            $stmt = mysqli_prepare($conn, "SELECT id FROM karyawan WHERE (username = ? OR email = ?) AND id != ?");
            mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = "Username atau email sudah digunakan oleh karyawan lain.";
            }
            mysqli_stmt_close($stmt);

            if (empty($errors)) {
                $stmt = mysqli_prepare($conn, "UPDATE karyawan SET nama = ?, username = ?, email = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "sssi", $nama, $username, $email, $id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Data karyawan berhasil diperbarui.";
                } else {
                    $errors[] = "Gagal memperbarui data karyawan.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // Insert new
            // Check for unique username/email
            $stmt = mysqli_prepare($conn, "SELECT id FROM karyawan WHERE username = ? OR email = ?");
            mysqli_stmt_bind_param($stmt, "ss", $username, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = "Username atau email sudah digunakan.";
            }
            mysqli_stmt_close($stmt);

            if (empty($errors)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO karyawan (nama, username, email) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sss", $nama, $username, $email);
                if (mysqli_stmt_execute($stmt)) {
                    $success_msg = "Karyawan baru berhasil ditambahkan.";
                } else {
                    $errors[] = "Gagal menambahkan karyawan baru.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM karyawan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Read employees
$result = mysqli_query($conn, "SELECT * FROM karyawan ORDER BY id ASC");
$karyawans = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Edit mode for form
$edit_mode = false;
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM karyawan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if ($edit_data) {
        $edit_mode = true;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Management Karyawan - Laundry Go</title>
<link rel="icon" type="image/png" href="asset/Group 3 - Copy.png" />
<link rel="stylesheet" href="karyawan.css">
<link rel="stylesheet" href="header.css">
<link rel="stylesheet" href="modal.css">
</head>
<body>
<?php include 'header.php'; ?>
<main>
    <h1>MANAGEMENT KARYAWAN</h1>

    <?php if ($success_msg): ?>
        <div role="alert" class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div role="alert" class="message error">
            <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h2>Form <?php echo $edit_mode ? "Edit" : "Tambah"; ?> Karyawan</h2>
    <form method="post" novalidate aria-label="<?php echo $edit_mode ? 'Form Edit Karyawan' : 'Form Tambah Karyawan'; ?>">
        <input type="hidden" name="id" value="<?php echo $edit_mode ? (int)$edit_data['id'] : ''; ?>" />
        <label for="nama">Nama Karyawan</label>
        <input type="text" id="nama" name="nama" required autocomplete="off" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nama']) : ''; ?>" placeholder="Masukkan nama karyawan" />

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autocomplete="off" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['username']) : ''; ?>" placeholder="Masukkan username karyawan" />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autocomplete="off" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['email']) : ''; ?>" placeholder="Masukkan email karyawan" />

        <button type="submit" class="btn btn-submit"><?php echo $edit_mode ? "Update" : "Tambah"; ?></button>
        <?php if ($edit_mode): ?>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-cancel" role="button">Batal</a>
        <?php endif; ?>
    </form>

    <h2>DAFTAR KARYAWAN</h2>
    <hr />
    <table role="table" aria-label="Daftar karyawan">
        <thead>
            <tr>
                <th scope="col">NO</th>
                <th scope="col">NAMA KARYAWAN</th>
                <th scope="col">USERNAME</th>
                <th scope="col">EMAIL</th>
                <th scope="col">ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($karyawans) === 0): ?>
                <tr><td colspan="5" style="text-align:center; padding:1.2rem;">Belum ada data karyawan.</td></tr>
            <?php else: ?>
                <?php foreach ($karyawans as $index => $kar): ?>
                    <tr>
                        <td data-label="NO"><?php echo $index + 1; ?></td>
                        <td data-label="NAMA KARYAWAN"><?php echo htmlspecialchars($kar['nama']); ?></td>
                        <td data-label="USERNAME"><?php echo htmlspecialchars($kar['username']); ?></td>
                        <td data-label="EMAIL"><?php echo htmlspecialchars($kar['email']); ?></td>
                        <td data-label="ACTION" style="white-space: nowrap;">
                            <a href="?edit=<?php echo $kar['id']; ?>" class="btn btn-edit" aria-label="Edit <?php echo htmlspecialchars($kar['nama']); ?>">Edit</a>
                            <a href="?delete=<?php echo $kar['id']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus karyawan <?php echo htmlspecialchars(addslashes($kar['nama'])); ?>?');" aria-label="Hapus <?php echo htmlspecialchars($kar['nama']); ?>">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>

