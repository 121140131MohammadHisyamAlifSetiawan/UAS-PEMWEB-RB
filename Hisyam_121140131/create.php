<?php
// Sertakan file konfigurasi
require_once "config.php";

// Tentukan variabel dan inisialisasi dengan nilai kosong
$name = $address = $salary = "";
$name_err = $address_err = $salary_err = "";

// Proses data formulir saat formulir dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi nama
    $input_name = trim($_POST["name"]);
    if (empty($input_name)) {
        $name_err = "Silakan masukkan nama.";
    } elseif (!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z\s]+$/")))) {
        $name_err = "Silakan masukkan nama yang valid.";
    } else {
        $name = $input_name;
    }

    // Validasi alamat
    $input_address = trim($_POST["address"]);
    if (empty($input_address)) {
        $address_err = "Silakan masukkan alamat.";
    } else {
        $address = $input_address;
    }

    // Validasi gaji
    $input_salary = trim($_POST["salary"]);
    if (empty($input_salary)) {
        $salary_err = "Silakan masukkan jumlah gaji.";
    } elseif (!ctype_digit($input_salary)) {
        $salary_err = "Silakan masukkan nilai bilangan bulat positif.";
    } else {
        $salary = $input_salary;
    }

    // Periksa kesalahan input sebelum memasukkan ke database
    if (empty($name_err) && empty($address_err) && empty($salary_err)) {
        // Persiapkan pernyataan insert
        $sql = "INSERT INTO karyawan (name, address, salary) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
            mysqli_stmt_bind_param($stmt, "sss", $param_name, $param_address, $param_salary);

            // Tetapkan parameter
            $param_name = $name;
            $param_address = $address;
            $param_salary = $salary;

            // Coba jalankan pernyataan yang telah disiapkan
            if (mysqli_stmt_execute($stmt)) {
                // Rekaman berhasil dibuat. Alihkan ke halaman utama
                header("location: admin.php");
                exit();
            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
        }

        // Tutup pernyataan
        mysqli_stmt_close($stmt);
    }

    // Tutup koneksi
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper {
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Input Data</h2>
                    <p>Silakan isi formulir ini dan kirim untuk menambahkan data karyawan ke database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name"
                                class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $name; ?>">
                            <span class="invalid-feedback">
                                <?php echo $name_err; ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="address"
                                class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>"><?php echo $address; ?></textarea>
                            <span class="invalid-feedback">
                                <?php echo $address_err; ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <label>Gaji</label>
                            <input type="text" name="salary"
                                class="form-control <?php echo (!empty($salary_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $salary; ?>">
                            <span class="invalid-feedback">
                                <?php echo $salary_err; ?>
                            </span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Kirim">
                        <a href="admin.php" class="btn btn-secondary ml-2">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
