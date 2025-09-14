<?php
session_start();
include 'config.php';

$user = $_POST['username'];
$pass = md5($_POST['password']);

$q = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass' LIMIT 1");

if (mysqli_num_rows($q) > 0) {
    $data = mysqli_fetch_assoc($q);

    // Simpan session dasar
    $_SESSION['username'] = $user;
    $_SESSION['role'] = $data['role'];

    // Cek role untuk redirect
    if ($data['role'] === 'admin') {
        header("Location: dashboard.php");

    } elseif ($data['role'] === 'guru') {
        header("Location: dashboard_guru.php");

    } elseif ($data['role'] === 'siswa') {
        // Cari id siswa dari tabel siswa berdasarkan nisn (username)
        $qSiswa = mysqli_query($conn, "SELECT id FROM siswa WHERE nisn='$user' LIMIT 1");
        if ($rowSiswa = mysqli_fetch_assoc($qSiswa)) {
            $_SESSION['siswa_id'] = $rowSiswa['id']; // simpan id siswa
        } else {
            // Kalau tidak ada di tabel siswa, hentikan
            echo "Data siswa tidak ditemukan. Hubungi admin.";
            exit;
        }

        header("Location: dashboard_siswa.php");

    } else {
        echo "Role tidak dikenali";
    }
    exit;

} else {
    echo "Login gagal, username atau password salah.";
}
?>
