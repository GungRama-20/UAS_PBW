

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Laundry Go</title>
  <link rel="icon" type="image/png" href="asset/Group 3 - Copy.png" />
  <link rel="stylesheet" href="login.css">
</head>
<body>
<?php
// Database credentials
$host = 'localhost';
$dbname = 'laundrygo';
$dbuser = 'root';
$dbpass = '';

// Create connection
$conn = new mysqli($host, $dbuser, $dbpass);

// Create database if not exists
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$dbCreated = $conn->query("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
if (!$dbCreated) {
    die("Database creation failed: " . $conn->error);
}

$conn->select_db($dbname);

// Create users table if not exists
$tableSql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;";

if (!$conn->query($tableSql)) {
    die("Table creation failed: " . $conn->error);
}

// To keep login state and messages
session_start();

$errorMsg = '';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errorMsg = "Username dan kata sandi wajib diisi.";
    } else {
        // Prepare statement to prevent SQL Injection
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userid, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $userid;
                $_SESSION['username'] = htmlspecialchars($username);
                // Redirect or success message
                header("Location: dashboard.php");
                exit;
            } else {
                $errorMsg = "Kata sandi salah.";
            }
        } else {
            $errorMsg = "Pengguna tidak ditemukan.";
        }
        $stmt->close();
    }
}

// Sample user creation for demo (you can remove this block after first load)
$demoUser = 'userdemo';
$demoPass = 'password123';
$checkDemoUser = $conn->prepare("SELECT id FROM users WHERE username = ?");
$checkDemoUser->bind_param('s', $demoUser);
$checkDemoUser->execute();
$checkDemoUser->store_result();
if ($checkDemoUser->num_rows === 0) {
    $insertUser = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $hashedPass = password_hash($demoPass, PASSWORD_DEFAULT);
    $insertUser->bind_param('ss', $demoUser, $hashedPass);
    $insertUser->execute();
    $insertUser->close();
}
$checkDemoUser->close();

$conn->close();

?>
    <div class="container" role="main" aria-label="Login Laundry Go">
        <section class="left-panel" aria-hidden="true">
            <h1>CEPAT BERSIH<br>LANGSUNG GO</h1>
            <p>Laundry Go Solusi Laundry Cepat dalam 3 Jam!</p>
        </section>
        <section class="right-panel">
            <div class="logo" aria-label="Laundry Go Logo">
                    <img src="asset/Group 3.png" alt="logo" height="64">
            </div>
            <h2>Login Akun</h2>
            <?php if ($errorMsg): ?>
                <div class="error-message" role="alert" aria-live="assertive"><?=htmlspecialchars($errorMsg)?></div>
            <?php endif; ?>
            <form method="POST" action="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" novalidate>
                <label for="username" class="sr-only">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" autocomplete="username" required autofocus aria-required="true" />
                
                <label for="password" class="sr-only">Kata Sandi</label>
                <input type="password" id="password" name="password" placeholder="Kata Sandi" autocomplete="current-password" required aria-required="true" />
                
                <button type="submit" class="login-btn" aria-label="Login ke akun Laundry Go">Login</button>
            </form>
        </section>
    </div>

    <style>
        /* Screen reader only text for accessible labels */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            border: 0;
        }
    </style>

</body>
</html>
