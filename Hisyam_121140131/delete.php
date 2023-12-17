<?php
// Proses operasi penghapusan setelah konfirmasi
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    // Sertakan file konfigurasi
    require_once "config.php";

    // Persiapkan pernyataan delete
    $sql = "DELETE FROM karyawan WHERE id = ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Binding variabel ke pernyataan yang telah disiapkan sebagai parameter
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Tetapkan parameter
        $param_id = trim($_POST["id"]);

        // Coba jalankan pernyataan yang telah disiapkan
        if (mysqli_stmt_execute($stmt)) {
            // Rekaman berhasil dihapus. Alihkan ke halaman utama
            header("location: admin.php");
            exit();
        } else {
            echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
        }
    }

    // Tutup pernyataan
    mysqli_stmt_close($stmt);

    // Tutup koneksi
    mysqli_close($link);
} else {
    // Periksa keberadaan parameter id
    if (empty(trim($_GET["id"]))) {
        // URL tidak mengandung parameter id. Alihkan ke halaman kesalahan
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hapus Data</title>
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
                    <h2 class="mt-5 mb-3">Hapus Data</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>" />
                            <p>Apakah Anda yakin ingin menghapus data karyawan ini?</p>
                            <p>
                                <input type="submit" value="Ya" class="btn btn-danger">
                                <a href="admin.php" class="btn btn-secondary">Tidak</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
