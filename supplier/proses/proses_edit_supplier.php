<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

// Validasi CSRF
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../data_supplier.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

include '../../koneksi_db.php';

$id_supplier   = isset($_POST['id_supplier'])   ? intval($_POST['id_supplier'])   : 0;
$nama_supplier = isset($_POST['nama_supplier']) ? trim($_POST['nama_supplier'])   : '';
$alamat        = isset($_POST['alamat'])         ? trim($_POST['alamat'])          : '';
$no_hp         = isset($_POST['no_hp'])          ? trim($_POST['no_hp'])           : '';

if ($id_supplier <= 0 || empty($nama_supplier) || empty($alamat) || empty($no_hp)) {
    header('Location: ../edit_supplier.php?id=' . $id_supplier . '&message=' . urlencode('Semua field harus diisi'));
    exit;
}

if (!preg_match('/^[\d\+\-]{7,20}$/', $no_hp)) {
    header('Location: ../edit_supplier.php?id=' . $id_supplier . '&message=' . urlencode('Nomor HP hanya boleh berisi angka dan tanda + atau -'));
    exit;
}

$sql = "UPDATE supplier SET nama_supplier = ?, alamat = ?, no_hp = ? WHERE id_supplier = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssi", $nama_supplier, $alamat, $no_hp, $id_supplier);
    if ($stmt->execute()) {
        header('Location: ../data_supplier.php?message=' . urlencode('Supplier berhasil diperbarui'));
    } else {
        header('Location: ../edit_supplier.php?id=' . $id_supplier . '&message=' . urlencode('Gagal memperbarui supplier'));
    }
    $stmt->close();
} else {
    header('Location: ../edit_supplier.php?id=' . $id_supplier . '&message=' . urlencode('Gagal memperbarui supplier'));
}
exit;
