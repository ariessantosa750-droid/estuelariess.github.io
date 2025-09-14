<?php
session_start();
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'guru'])) {
    header("Location: index.php");
    exit;
}

include 'config.php';
require 'vendor/phpqrcode/qrlib.php';

/* ==== Tambah kolom no_wa di tabel siswa (jalankan sekali di phpMyAdmin) ====
ALTER TABLE siswa ADD no_wa VARCHAR(20) AFTER kelas;
============================================================================= */

/* ==== Buat tabel users jika belum ada ====
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL,
  role ENUM('admin','siswa') DEFAULT 'siswa'
);
============================================================================= */

// Proses simpan (tambah baru)
if (isset($_POST['simpan'])) {
    $nis   = $_POST['nis'];
    $nisn  = $_POST['nisn'];
    $nama  = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $no_wa = $_POST['no_wa'];

    mysqli_query($conn, "INSERT INTO siswa (nis, nisn, nama, kelas, no_wa, status) 
                         VALUES ('$nis', '$nisn', '$nama', '$kelas', '$no_wa', 'aktif')");

    // Buat akun user untuk siswa
    $username = $nisn;
    $password = md5($nisn);
    $role     = 'siswa';

    $cek_user = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' LIMIT 1");
    if (mysqli_num_rows($cek_user) == 0) {
        mysqli_query($conn, "INSERT INTO users (username, nama, password, role) 
                             VALUES ('$username', '$nama', '$password', '$role')");
    }

    // Generate QR Code
    $qr_dir = "assets/qr/";
    if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, true);
    QRcode::png($nisn, $qr_dir . "$nisn.png", QR_ECLEVEL_L, 4);

    header("Location: siswa.php");
    exit;
}

// Proses update data (edit)
if (isset($_POST['update'])) {
    $id    = intval($_POST['id']);
    $nis   = $_POST['nis'];
    $nisn  = $_POST['nisn'];
    $nama  = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $no_wa = $_POST['no_wa'];

    $res_old = mysqli_query($conn, "SELECT nisn FROM siswa WHERE id=$id LIMIT 1");
    $old     = mysqli_fetch_assoc($res_old);
    $old_nisn = $old['nisn'];

    mysqli_query($conn, "UPDATE siswa 
                         SET nis='$nis', nisn='$nisn', nama='$nama', kelas='$kelas', no_wa='$no_wa' 
                         WHERE id=$id");

    mysqli_query($conn, "UPDATE users 
                         SET username='$nisn', nama='$nama', password=md5('$nisn') 
                         WHERE username='$old_nisn' AND role='siswa'");

    $qr_dir = "assets/qr/";
    if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, true);
    QRcode::png($nisn, $qr_dir . "$nisn.png", QR_ECLEVEL_L, 4);

    header("Location: siswa.php");
    exit;
}

// Tandai siswa keluar
if (isset($_GET['keluar'])) {
    $id = intval($_GET['keluar']);
    $res = mysqli_query($conn, "SELECT nisn FROM siswa WHERE id=$id LIMIT 1");
    $data = mysqli_fetch_assoc($res);
    $nisn_keluar = $data['nisn'];

    mysqli_query($conn, "UPDATE siswa SET status='keluar' WHERE id=$id");
    mysqli_query($conn, "DELETE FROM users WHERE username='$nisn_keluar' AND role='siswa'");

    header("Location: siswa.php");
    exit;
}

// Generate akun massal
if (isset($_POST['generate_akun'])) {
    $q_siswa = mysqli_query($conn, "SELECT nisn, nama FROM siswa WHERE status='aktif'");
    $count = 0;
    while ($s = mysqli_fetch_assoc($q_siswa)) {
        $username = $s['nisn'];
        $nama     = $s['nama'];
        $password = md5($s['nisn']);
        $role     = 'siswa';

        $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' LIMIT 1");
        if (mysqli_num_rows($cek) == 0) {
            mysqli_query($conn, "INSERT INTO users (username, nama, password, role) 
                                 VALUES ('$username', '$nama', '$password', '$role')");
            $count++;
        }
    }
    echo "<script>alert('Generate akun selesai. $count akun baru dibuat.');window.location='siswa.php';</script>";
    exit;
}

// Ambil data untuk edit jika ada
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM siswa WHERE id=$id LIMIT 1");
    $edit_data = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

  <h2 class="text-center mb-4">Data Siswa</h2>

  <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

  <!-- Form Input / Edit -->
  <form method="post" class="row g-2 mb-4">
    <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
    <div class="col-6 col-md-2">
      <input type="number" name="nis" class="form-control" placeholder="NIS" required value="<?= $edit_data['nis'] ?? '' ?>">
    </div>
    <div class="col-6 col-md-2">
      <input type="number" name="nisn" class="form-control" placeholder="NISN" required value="<?= $edit_data['nisn'] ?? '' ?>">
    </div>
    <div class="col-12 col-md-3">
      <input type="text" name="nama" class="form-control" placeholder="Nama" required value="<?= $edit_data['nama'] ?? '' ?>">
    </div>
    <div class="col-6 col-md-2">
      <input type="text" name="kelas" class="form-control" placeholder="Kelas" required value="<?= $edit_data['kelas'] ?? '' ?>">
    </div>
    <div class="col-6 col-md-3">
      <input type="text" name="no_wa" class="form-control" placeholder="Nomor WhatsApp (6285xxxx)" value="<?= $edit_data['no_wa'] ?? '' ?>">
    </div>
    <div class="col-12 col-md-2">
      <?php if ($edit_data): ?>
        <button type="submit" name="update" class="btn btn-warning w-100">Update</button>
        <a href="siswa.php" class="btn btn-secondary w-100 mt-2">Batal</a>
      <?php else: ?>
        <button type="submit" name="simpan" class="btn btn-primary w-100">Simpan</button>
      <?php endif; ?>
    </div>
  </form>

  <form method="post" class="mb-3">
    <button type="submit" name="generate_akun" class="btn btn-dark">⚡ Generate Akun Siswa</button>
  </form>

  <a href="cetak_kartu.php" class="btn btn-success mb-3" target="_blank">Cetak Semua Kartu QR</a>
  <a href="siswa_keluar.php" class="btn btn-outline-danger mb-3">Lihat Siswa Keluar</a>
  <a href="import_siswa.php" class="btn btn-success mb-3">📥 Import dari Excel</a>

  <!-- Tabel Data Siswa -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light text-center">
        <tr>
          <th>NIS</th>
          <th>NISN</th>
          <th>Nama</th>
          <th>Kelas</th>
          <th>QR Code</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $q = mysqli_query($conn, "SELECT * FROM siswa WHERE status='aktif' ORDER BY nama ASC");
        while ($row = mysqli_fetch_assoc($q)) {
          echo "<tr>
            <td>{$row['nis']}</td>
            <td>{$row['nisn']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['kelas']}</td>
            <td class='text-center'>
              <a href='assets/qr/{$row['nisn']}.png' target='_blank'>
                <img src='assets/qr/{$row['nisn']}.png' width='50'>
              </a>
            </td>
            <td class='text-center'>
              <a href='siswa.php?edit={$row['id']}' class='btn btn-info btn-sm'>Edit</a>
              <a href='siswa.php?keluar={$row['id']}' class='btn btn-warning btn-sm' onclick='return confirm(\"Yakin siswa ini keluar?\")'>Keluar</a>
            </td>
          </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

</body>
</html>
