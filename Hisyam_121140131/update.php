<?php
// Sertakan file konfigurasi
require_once "config.php";

// Tentukan variabel dan inisialisasi dengan nilai kosong
$name = $address = $salary = "";
$name_err = $address_err = $salary_err = "";

// Memproses data formulir ketika formulir dikirimkan
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Dapatkan nilai input tersembunyi
    $id = $_POST["id"];

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
        // Persiapkan pernyataan update
        $sql = "UPDATE karyawan SET name=?, address=?, salary=? WHERE id=?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
            mysqli_stmt_bind_param($stmt, "sssi", $param_name, $param_address, $param_salary, $param_id);

            // Tetapkan parameter
            $param_name = $name;
            $param_address = $address;
            $param_salary = $salary;
            $param_id = $id;

            // Coba jalankan pernyataan yang telah disiapkan
            if (mysqli_stmt_execute($stmt)) {
                // Rekaman berhasil diperbarui. Redirect ke halaman utama
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
} else {
    // Periksa keberadaan parameter id sebelum memproses lebih lanjut
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Dapatkan parameter URL
        $id = trim($_GET["id"]);

        // Persiapkan pernyataan select
        $sql = "SELECT * FROM karyawan WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
            mysqli_stmt_bind_param($stmt, "i", $param_id);

            // Tetapkan parameter
            $param_id = $id;

            // Coba jalankan pernyataan yang telah disiapkan
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    /* Ambil baris hasil sebagai array asosiatif. Karena hasil set
                    hanya berisi satu baris, kita tidak perlu menggunakan loop while */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                    // Ambil nilai bidang individu
                    $name = $row["name"];
                    $address = $row["address"];
                    $salary = $row["salary"];
                } else {
                    // URL tidak berisi id yang valid. Redirect ke halaman error
                    header("location: error.php");
                    exit();
                }

            } else {
                echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
        }

        // Tutup pernyataan
        mysqli_stmt_close($stmt);

        // Tutup koneksi
        mysqli_close($link);
    } else {
        // URL tidak berisi parameter id. Redirect ke halaman error
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Perbarui Rekaman</title>
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
                    <h2 class="mt-5">Perbarui Rekaman</h2>
                    <p>Silakan edit nilai input dan kirimkan untuk memperbarui rekaman karyawan.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
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
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="admin.php" class="btn btn-secondary ml-2">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>