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

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data_barang.php?message=Akses tidak sah!');
    exit;
}

// Validasi CSRF token
if (!isset($_POST['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../data_barang.php?message=Akses tidak sah!');
    exit;
}

include '../../koneksi_db.php';

$id_barang   = isset($_POST['id_barang'])   ? intval($_POST['id_barang'])   : 0;
$nama_barang = isset($_POST['nama_barang']) ? trim($_POST['nama_barang'])   : '';
$id_kategori = isset($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 0;
$stok        = isset($_POST['stok'])        ? intval($_POST['stok'])        : 0;
$satuan      = isset($_POST['satuan'])      ? trim($_POST['satuan'])        : '';
$harga       = isset($_POST['harga'])       ? intval($_POST['harga'])       : 0;

if ($id_barang <= 0 || empty($nama_barang) || $id_kategori <= 0 || $harga < 0 || $stok < 0) {
    header('Location: ../data_barang.php?message=Semua field harus diisi dengan benar');
    exit;
}

$nama_barang = substr($nama_barang, 0, 100);
$satuan      = substr($satuan, 0, 50);

$sql = "UPDATE barang SET nama_barang = ?, id_kategori = ?, stok = ?, satuan = ?, harga = ? WHERE id_barang = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("siisii", $nama_barang, $id_kategori, $stok, $satuan, $harga, $id_barang);
    if ($stmt->execute()) {
        header('Location: ../data_barang.php?message=Barang berhasil diperbarui');
    } else {
        header('Location: ../data_barang.php?message=Gagal memperbarui barang');
    }
    $stmt->close();
} else {
    header('Location: ../data_barang.php?message=Gagal memperbarui barang');
}
?>