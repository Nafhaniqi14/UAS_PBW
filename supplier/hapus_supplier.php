<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: data_supplier.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

// Validasi CSRF token
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: data_supplier.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

include '../koneksi_db.php';

$id_supplier = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_supplier > 0) {
    $sql = "DELETE FROM supplier WHERE id_supplier = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_supplier);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Location: data_supplier.php?message=' . urlencode('Supplier berhasil dihapus'));
            } else {
                header('Location: data_supplier.php?message=' . urlencode('ID supplier tidak valid'));
            }
        } else {
            header('Location: data_supplier.php?message=' . urlencode('Gagal menghapus supplier'));
        }
        $stmt->close();
    } else {
        header('Location: data_supplier.php?message=' . urlencode('Gagal menghapus supplier'));
    }
} else {
    header('Location: data_supplier.php?message=' . urlencode('ID supplier tidak valid'));
}
exit;
