<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data_kategori.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

// Validasi CSRF
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../data_kategori.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

include '../../koneksi_db.php';

$id_kategori   = isset($_POST['id_kategori']) && is_numeric($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 0;
$nama_kategori = isset($_POST['nama_kategori']) ? trim($_POST['nama_kategori']) : '';

if ($nama_kategori === '') {
    header('Location: ../data_kategori.php?message=' . urlencode('Semua field harus diisi'));
    exit;
}

$nama_kategori = substr($nama_kategori, 0, 100);

if ($id_kategori > 0) {
    $sql = "UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('si', $nama_kategori, $id_kategori);
        if ($stmt->execute()) {
            header('Location: ../data_kategori.php?message=' . urlencode('Kategori berhasil diperbarui'));
        } else {
            header('Location: ../data_kategori.php?message=' . urlencode('Gagal memperbarui kategori'));
        }
        $stmt->close();
    } else {
        header('Location: ../data_kategori.php?message=' . urlencode('Gagal memperbarui kategori'));
    }
} else {
    $sql = "INSERT INTO kategori (nama_kategori) VALUES (?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $nama_kategori);
        if ($stmt->execute()) {
            header('Location: ../data_kategori.php?message=' . urlencode('Kategori berhasil ditambahkan'));
        } else {
            header('Location: ../data_kategori.php?message=' . urlencode('Gagal menambahkan kategori'));
        }
        $stmt->close();
    } else {
        header('Location: ../data_kategori.php?message=' . urlencode('Gagal menambahkan kategori'));
    }
}
exit;
