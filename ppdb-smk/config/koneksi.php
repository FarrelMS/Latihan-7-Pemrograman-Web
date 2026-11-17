<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "db_psb";
$port = 3307;

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
