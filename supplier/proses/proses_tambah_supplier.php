<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

// Validasi CSRF
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../tambah_supplier.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

include '../../koneksi_db.php';

$nama_supplier = isset($_POST['nama_supplier']) ? trim($_POST['nama_supplier']) : '';
$alamat        = isset($_POST['alamat'])         ? trim($_POST['alamat'])         : '';
$no_hp         = isset($_POST['no_hp'])          ? trim($_POST['no_hp'])          : '';

if (empty($nama_supplier) || empty($alamat) || empty($no_hp)) {
    header('Location: ../tambah_supplier.php?message=' . urlencode('Semua field harus diisi'));
    exit;
}

// Validasi format no_hp: hanya angka, +, -, minimal 7 karakter
if (!preg_match('/^[\d\+\-]{7,20}$/', $no_hp)) {
    header('Location: ../tambah_supplier.php?message=' . urlencode('Nomor HP hanya boleh berisi angka dan tanda + atau -'));
    exit;
}

$sql = "INSERT INTO supplier (nama_supplier, alamat, no_hp) VALUES (?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sss", $nama_supplier, $alamat, $no_hp);
    if ($stmt->execute()) {
        header('Location: ../data_supplier.php?message=' . urlencode('Supplier berhasil ditambahkan'));
    } else {
        header('Location: ../tambah_supplier.php?message=' . urlencode('Gagal menambahkan supplier'));
    }
    $stmt->close();
} else {
    header('Location: ../tambah_supplier.php?message=' . urlencode('Gagal menambahkan supplier'));
}
exit;
