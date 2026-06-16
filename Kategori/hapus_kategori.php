<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: data_kategori.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: data_kategori.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

include '../koneksi_db.php';

$id_kategori = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_kategori <= 0) {
    header('Location: data_kategori.php?message=' . urlencode('ID kategori tidak valid'));
    exit;
}

$sql = "DELETE FROM kategori WHERE id_kategori = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('i', $id_kategori);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header('Location: data_kategori.php?message=' . urlencode('Kategori berhasil dihapus'));
        } else {
            header('Location: data_kategori.php?message=' . urlencode('ID kategori tidak valid'));
        }
    } else {
        $message = ($conn->errno === 1451)
            ? 'Gagal menghapus kategori karena masih digunakan'
            : 'Gagal menghapus kategori';
        header('Location: data_kategori.php?message=' . urlencode($message));
    }
    $stmt->close();
} else {
    header('Location: data_kategori.php?message=' . urlencode('Gagal menghapus kategori'));
}
exit;
