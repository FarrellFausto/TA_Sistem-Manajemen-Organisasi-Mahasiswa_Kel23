<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_organisasi_ta_prak_sbd";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal bray: " . mysqli_connect_error());
}
?>