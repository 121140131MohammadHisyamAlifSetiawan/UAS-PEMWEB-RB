<?php
// Sertakan file konfigurasi
require_once "config.php";

// Tentukan variabel dan inisialisasi dengan nilai kosong
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Memproses data formulir ketika formulir dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Silakan masukkan nama pengguna.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Nama pengguna hanya boleh berisi huruf, angka, dan garis bawah.";
    } else {
        // Persiapkan pernyataan select
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Tetapkan parameter
            $param_username = trim($_POST["username"]);

            // Coba jalankan pernyataan yang telah disiapkan
            if (mysqli_stmt_execute($stmt)) {
                /* simpan hasil */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Nama pengguna ini sudah digunakan.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }

            // Tutup pernyataan
            mysqli_stmt_close($stmt);
        }
    }

    // Validasi password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Silakan masukkan kata sandi.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Kata sandi harus memiliki setidaknya 6 karakter.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validasi konfirmasi password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Silakan konfirmasi kata sandi.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Kata sandi tidak cocok.";
        }
    }

    // Periksa kesalahan input sebelum memasukkan ke database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

        // Persiapkan pernyataan insert
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            // Tetapkan parameter
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Membuat hash kata sandi

            // Coba jalankan pernyataan yang telah disiapkan
            if (mysqli_stmt_execute($stmt)) {
                // Alihkan ke halaman login
                header("location: login.php");
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
    <title>Daftar</title>
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
        <h2>Daftar</h2>
        <p>Silakan isi formulir ini untuk membuat akun.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nama Pengguna</label>
                <input type="text" name="username"
                    class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $username; ?>">
                <span class="invalid-feedback">
                    <?php echo $username_err; ?>
                </span>
            </div>
            <div class="form-group">
                <label>Kata Sandi</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is
-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback">
                    <?php echo $password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <label>Konfirmasi Kata Sandi</label>
                <input type="password" name="confirm_password"
                    class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback">
                    <?php echo $confirm_password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Daftar">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Sudah memiliki akun? <a href="login.php">Login di sini</a>.</p>
        </form>
    </div>
</body>

</html>