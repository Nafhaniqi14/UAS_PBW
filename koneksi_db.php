<?php
$conn = mysqli_connect("localhost", "root", "", "inventaris_barang");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>