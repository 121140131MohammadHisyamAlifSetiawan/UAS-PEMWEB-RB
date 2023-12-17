<?php
// Periksa keberadaan parameter id sebelum memproses lebih lanjut
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Sertakan file konfigurasi
    require_once "config.php";

    // Persiapkan pernyataan select
    $sql = "SELECT * FROM karyawan WHERE id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Tetapkan parameter
        $param_id = trim($_GET["id"]);

        // Coba jalankan pernyataan yang telah disiapkan
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                /* Ambil baris hasil sebagai array asosiatif. Karena set hasil
                hanya berisi satu baris, kita tidak perlu menggunakan loop while */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Ambil nilai bidang individu
                $name = $row["name"];
                $address = $row["address"];
                $salary = $row["salary"];
            } else {
                // URL tidak mengandung parameter id yang valid. Alihkan ke halaman kesalahan
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
    // URL tidak mengandung parameter id. Alihkan ke halaman kesalahan
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lihat Data</title>
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
                    <h1 class="mt-5 mb-3">Lihat Data</h1>
                    <div class="form-group">
                        <label>Nama</label>
                        <p><b>
                                <?php echo $row["name"]; ?>
                            </b></p>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <p><b>
                                <?php echo $row["address"]; ?>
                            </b></p>
                    </div>
                    <div class="form-group">
                        <label>Gaji</label>
                        <p><b>
                                <?php echo $row["salary"]; ?>
                            </b></p>
                    </div>
                    <p><a href="admin.php" class="btn btn-primary">Kembali</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
