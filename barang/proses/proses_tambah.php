<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../../index.php');
    exit;
}
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../data_barang.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data_barang.php?message=Akses tidak sah!');
    exit;
}

if (!isset($_POST['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../data_barang.php?message=Akses tidak sah!');
    exit;
}

include '../../koneksi_db.php';

$nama_barang = isset($_POST['nama_barang']) ? trim($_POST['nama_barang'])   : '';
$id_kategori = isset($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 0;
$stok        = isset($_POST['stok'])        ? intval($_POST['stok'])        : 0;
$satuan      = isset($_POST['satuan'])      ? trim($_POST['satuan'])        : '';
$harga       = isset($_POST['harga'])       ? intval($_POST['harga'])       : 0;

if (empty($nama_barang) || $id_kategori <= 0 || $harga < 0 || $stok < 0) {
    header('Location: ../data_barang.php?message=Semua field harus diisi dengan benar');
    exit;
}

$nama_barang = substr($nama_barang, 0, 100);
$satuan      = substr($satuan, 0, 50);

$sql = "INSERT INTO barang (nama_barang, id_kategori, stok, satuan, harga) VALUES (?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("siisi", $nama_barang, $id_kategori, $stok, $satuan, $harga);
    if ($stmt->execute()) {
        header('Location: ../data_barang.php?message=Barang berhasil ditambahkan');
    } else {
        header('Location: ../data_barang.php?message=Gagal menambahkan barang');
    }
    $stmt->close();
} else {
    header('Location: ../data_barang.php?message=Gagal menambahkan barang');
}
?>