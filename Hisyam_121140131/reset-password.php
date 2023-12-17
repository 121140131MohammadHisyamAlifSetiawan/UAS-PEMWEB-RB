<?php
// Mulai sesi
session_start();

// Periksa apakah pengguna sudah login, jika belum, redirect ke halaman login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Sertakan file konfigurasi
require_once "config.php";

// Tentukan variabel dan inisialisasi dengan nilai kosong
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

// Memproses data formulir ketika formulir dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi kata sandi baru
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Silakan masukkan kata sandi baru.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Kata sandi harus memiliki setidaknya 6 karakter.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validasi konfirmasi kata sandi
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Silakan konfirmasi kata sandi.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Kata sandi tidak cocok.";
        }
    }

    // Periksa kesalahan input sebelum memperbarui database
    if (empty($new_password_err) && empty($confirm_password_err)) {
        // Persiapkan pernyataan update
        $sql = "UPDATE users SET password = ? WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);

            // Tetapkan parameter
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            // Coba jalankan pernyataan yang telah disiapkan
            if (mysqli_stmt_execute($stmt)) {
                // Kata sandi berhasil diperbarui. Hancurkan sesi, dan redirect ke halaman login
                session_destroy();
                header("location: login.php");
                exit();
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }

            // Tutup pernyataan
            mysqli_stmt_close($stmt);
        }
    }

    // Tutup koneksi
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Silakan isi formulir ini untuk mereset kata sandi Anda.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Kata Sandi Baru</label>
                <input type="password" name="new_password"
                    class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $new_password; ?>">
                <span class="invalid-feedback">
                    <?php echo $new_password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <label>Konfirmasi Kata Sandi</label>
                <input type="password" name="confirm_password"
                    class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback">
                    <?php echo $confirm_password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link ml-2" href="index.php">Batal</a>
            </div>
        </form>
    </div>
</body>

</html>
