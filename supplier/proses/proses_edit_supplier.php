<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

include 'koneksi_db.php';

$id_supplier = isset($_POST['id_supplier']) ? intval($_POST['id_supplier']) : 0;
$nama_supplier = isset($_POST['nama_supplier']) ? trim($_POST['nama_supplier']) : '';
$alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
$no_hp = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';

if ($id_supplier <= 0 || empty($nama_supplier) || empty($alamat) || empty($no_hp)) {
    header('Location: data_supplier.php?message=Semua field harus diisi');
    exit;
}

$sql = "UPDATE supplier SET nama_supplier = ?, alamat = ?, no_hp = ? WHERE id_supplier = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssi", $nama_supplier, $alamat, $no_hp, $id_supplier);
    if ($stmt->execute()) {
        header('Location: data_supplier.php?message=Supplier berhasil diperbarui');
    } else {
        header('Location: data_supplier.php?message=Gagal memperbarui supplier');
    }
    $stmt->close();
} else {
    header('Location: data_supplier.php?message=Error: ' . $conn->error);
}
?>

