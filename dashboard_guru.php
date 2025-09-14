<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'] ?? '';

// Batasi hanya admin & guru
if (!in_array($role, ['guru','admin'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Guru Absensi QR</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    body {
      font-family: sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f4f4;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .marquee-container {
      background: #ff9800;
      overflow: hidden;
      white-space: nowrap;
      padding: 10px 0;
    }
    .marquee-text {
      display: inline-block;
      padding-left: 100%;
      animation: marquee 15s linear infinite;
      color: white;
      font-weight: bold;
      font-size: 16px;
    }
    @keyframes marquee {
      0%   { transform: translateX(0); }
      100% { transform: translateX(-100%); }
    }
    header {
      background-color: #03016cff;
      color: white;
      padding: 15px;
      text-align: center;
    }
    h2 { margin: 20px; text-align: center; }
    ul {
      list-style-type: none;
      padding: 0; margin: 20px auto;
      display: flex; flex-direction: column;
      gap: 10px; max-width: 400px;
    }
    li a {
      display: flex; align-items: center; justify-content: center;
      gap: 8px; background-color: #fff; color: #333;
      text-decoration: none; padding: 15px;
      border-radius: 8px; font-weight: bold;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: background-color 0.3s ease;
      border: 4px solid transparent;
    }
    li a:hover { background-color: #e0e0e0; }
    .siswa { border-color: orange; }
    .scan { border-color: green; }
    .sia { border-color: blue; }
    .jam { border-color: purple; }
    .rekap { border-color: black; }
    .grafik { border-color: black; }
    .prosentase { border-color: black; }
    .libur { border-color: red; }
    .excel { border-color: green; }
    .wa { border-color: green; }
    .logout { border-color: red; }
    @media (min-width: 600px) {
      ul { flex-direction: row; flex-wrap: wrap; justify-content: center; }
      li { flex: 1 1 40%; margin: 5px; }
    }
    footer {
      margin-top: auto; background: #333;
      color: white; text-align: center;
      padding: 10px; font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="marquee-container">
    <span class="marquee-text">
      Kehadiran Bapak/Ibu Guru membersamai siswa belajar tidak akan pernah dapat digantikan oleh Robot AI
    </span>
  </div>

  <header>
    <h1>Dashboard Guru</h1>
  </header>

  <h2>Menu</h2>
  <ul>
    <li><a href="siswa_2.php" class="siswa"><i class="fa-solid fa-user-graduate"></i> Data Siswa</a></li>
    <li><a href="scan_2.php" class="scan"><i class="fa-solid fa-qrcode"></i> SCAN QR</a></li>
    <li><a href="scan_wa_2.php" class="scan"><i class="fa-solid fa-qrcode"></i> SCAN QR + WA</a></li>
    <li><a href="scan_wa_api_2.php" class="scan"><i class="fa-solid fa-qrcode"></i> SCAN QR + WA API OTOMATIS</a></li>
    <li><a href="belum_absensi_2.php" class="libur"><i class="fa-solid fa-user"></i> Siswa Belum Hadir</a></li>
    <li><a href="absensi_2.php" class="sia"><i class="fa-solid fa-clipboard-check"></i> Isi S/I/A</a></li>
    <li><a href="jam_absensi_2.php" class="jam"><i class="fa-solid fa-clock"></i> Jam Waktu Absensi</a></li>
    <li><a href="rekap_bulanan_2.php" class="rekap"><i class="fa-solid fa-calendar-days"></i> Rekap Bulanan</a></li>
    <li><a href="grafik_2.php" class="grafik"><i class="fa-solid fa-chart-line"></i> Grafik</a></li>
    <li><a href="hadir_2.php" class="prosentase"><i class="fa-solid fa-chart-pie"></i> Prosentase Kehadiran</a></li>
    <li><a href="libur.php" class="libur"><i class="fa-solid fa-plane"></i> Hari Libur</a></li>
    <li><a href="export.php" class="excel"><i class="fa-solid fa-file-excel"></i> Export Excel</a></li>
    <li><a href="wa-wali-siswa.php" class="wa"><i class="fa-brands fa-whatsapp"></i> Kirim WA Orang Tua/Wali Siswa</a></li>
    <li><a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
  </ul>

  <footer>
    Versi Aplikasi: 4.00
  </footer>
</body>
</html>
