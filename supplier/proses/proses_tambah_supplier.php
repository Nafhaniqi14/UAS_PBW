<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

// File ini berada di /supplier/proses_tambah_supplier.php
include '../koneksi_db.php';

$nama_supplier = isset($_POST['nama_supplier']) ? trim($_POST['nama_supplier']) : '';
$alamat        = isset($_POST['alamat'])         ? trim($_POST['alamat'])         : '';
$no_hp         = isset($_POST['no_hp'])          ? trim($_POST['no_hp'])          : '';

if (empty($nama_supplier) || empty($alamat) || empty($no_hp)) {
    header('Location: tambah_supplier.php?message=' . urlencode('Semua field harus diisi'));
    exit;
}

$sql = "INSERT INTO supplier (nama_supplier, alamat, no_hp) VALUES (?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sss", $nama_supplier, $alamat, $no_hp);
    if ($stmt->execute()) {
        header('Location: data_supplier.php?message=' . urlencode('Supplier berhasil ditambahkan'));
    } else {
        header('Location: tambah_supplier.php?message=' . urlencode('Gagal menambahkan supplier'));
    }
    $stmt->close();
} else {
    header('Location: tambah_supplier.php?message=' . urlencode('Error: ' . $conn->error));
}
exit;
