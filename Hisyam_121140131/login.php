<?php
// Inisialisasi sesi
session_start();

// Periksa apakah pengguna sudah login, jika ya, alihkan ke halaman selamat datang
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

// Sertakan file konfigurasi
require_once "config.php";

// Tentukan variabel dan inisialisasi dengan nilai kosong
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Proses data formulir saat formulir dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Periksa apakah nama pengguna kosong
    if (empty(trim($_POST["username"]))) {
        $username_err = "Silakan masukkan nama pengguna.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Periksa apakah kata sandi kosong
    if (empty(trim($_POST["password"]))) {
        $password_err = "Silakan masukkan kata sandi Anda.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validasi kredensial
    if (empty($username_err) && empty($password_err)) {
        // Persiapkan pernyataan select
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Tetapkan parameter
            $param_username = $username;

            // Coba jalankan pernyataan yang telah disiapkan
            if (mysqli_stmt_execute($stmt)) {
                // Simpan hasil
                mysqli_stmt_store_result($stmt);

                // Periksa apakah nama pengguna ada, jika ya, verifikasi kata sandi
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Binding variabel hasil
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Kata sandi benar, jadi mulai sesi baru
                            session_start();

                            // Simpan data dalam variabel sesi
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Alihkan pengguna ke halaman selamat datang
                            header("location: index.php");
                        } else {
                            // Kata sandi tidak valid, tampilkan pesan kesalahan umum
                            $login_err = "Nama pengguna atau kata sandi tidak valid.";
                        }
                    }
                } else {
                    // Nama pengguna tidak ada, tampilkan pesan kesalahan umum
                    $login_err = "Nama pengguna atau kata sandi tidak valid.";
                }
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
    <title>Login</title>
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
        <h2>Login</h2>
        <p>Silakan isi kredensial Anda untuk login.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

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
                <input type="password" name="password"
                    class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback">
                    <?php echo $password_err; ?>
                </span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Belum memiliki akun? <a href="register.php">Daftar sekarang</a>.</p>
        </form>
    </div>
</body>

</html>
