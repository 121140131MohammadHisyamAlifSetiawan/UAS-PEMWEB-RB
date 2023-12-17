<?php
// Inisialisasi sesi
session_start();

// Periksa apakah pengguna sudah login, jika belum, alihkan ke halaman login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Selamat Datang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1 class="my-5">Hi, <b>
            <?php echo htmlspecialchars($_SESSION["username"]); ?>
        </b>. Selamat datang di situs kami.</h1>
    <p>
        <a href="admin.php" class="btn btn-primary">Halaman Admin</a>
        <a href="reset-password.php" class="btn btn-warning">Reset Kata Sandi Anda</a>
        <a href="logout.php" class="btn btn-danger ml-3">Keluar dari Akun Anda</a>
    </p>
</body>

</html>