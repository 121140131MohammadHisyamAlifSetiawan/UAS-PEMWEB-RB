<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'uas_pemweb');
 
/* Upaya untuk terhubung ke database MySQL */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Periksa koneksi
if($link === false){
    die("ERROR: Tidak dapat terhubung. " . mysqli_connect_error());
}
?>
