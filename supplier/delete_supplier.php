<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

include 'koneksi_db.php';

$id_supplier = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_supplier > 0) {
    $sql = "DELETE FROM supplier WHERE id_supplier = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_supplier);
        if ($stmt->execute()) {
            header('Location: data_supplier.php?message=Supplier berhasil dihapus');
        } else {
            header('Location: data_supplier.php?message=Gagal menghapus supplier');
        }
        $stmt->close();
    }
} else {
    header('Location: data_supplier.php?message=ID supplier tidak valid');
}
?>

