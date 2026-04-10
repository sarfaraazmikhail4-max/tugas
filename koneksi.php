<?php

$host = "sql308.infinityfree.com";
$user = "if0_41626389";
$pass = "pierrejuan99";
$db   = "if0_41626389_db_ukk_pengaduan";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (!function_exists('input')) {
    function input($data) {
        global $koneksi;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = mysqli_real_escape_string($koneksi, $data);
        return $data;
    }
}
?>