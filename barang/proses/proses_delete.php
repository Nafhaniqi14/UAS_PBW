<?php
session_start();
if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true) {
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

$id_barang = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_barang > 0) {
    $sql = "DELETE FROM barang WHERE id_barang = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_barang);
        if ($stmt->execute()) {
            header('Location: ../data_barang.php?message=Barang berhasil dihapus');
        } else {
            header('Location: ../data_barang.php?message=Gagal menghapus barang');
        }
        $stmt->close();
    }
} else {
    header('Location: ../data_barang.php?message=ID barang tidak valid');
}
?>