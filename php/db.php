<?php
$host = "localhost";
$user = "root";          
$pass = "";              
$db   = "tanduria";      

// Koneksi ke MySQL
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>
