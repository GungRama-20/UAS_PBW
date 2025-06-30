<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<header class="header" role="banner">
  <a href="dashboard.php" class="logo" style="display: flex; align-items: center;">
    <img src="asset/Group 3 (1).png" alt="Laundry Go blue water wave logo icon" height="64"/>
  </a>

  <nav role="navigation" aria-label="Primary navigation menu">
    <ul>
      <li><a href="riwayattransaksi.php" aria-current="page">Riwayat Transaksi</a></li>
      <li><a href="karyawan.php">Manage Karyawan</a></li>
      <li><a href="daftarpaket.php">Daftar Paket</a></li>
    </ul>
  </nav>

  <div class="admin" aria-label="Logged in user name">
    <ul class="nav-menu">
      <li>
        <a href="#" class="admin-name"><?= isset($_SESSION['master']) ? ucfirst(htmlspecialchars($_SESSION['master'])) : 'Admin' ?></a>
        <ul class="dropdown-menu">
          <li><a href="about.php">Tentang Kami</a></li>
          <li><a href="#" onclick="confirmLogout(event)">Logout</a></li>
        </ul>
      </li>
    </ul>
  </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmLogout(event) {
  event.preventDefault();
  Swal.fire({
    title: 'Keluar dari akun?',
    text: "Apakah Anda yakin ingin logout?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, Logout',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = 'logout.php';
    }
  });
}
</script>
