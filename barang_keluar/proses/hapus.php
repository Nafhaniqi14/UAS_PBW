<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../../index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data_barang_keluar.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

if (!isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../data_barang_keluar.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

include '../../koneksi_db.php';

$id_keluar = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_keluar <= 0) {
    header('Location: ../data_barang_keluar.php?message=' . urlencode('ID barang keluar tidak valid'));
    exit;
}

// Ambil data barang keluar untuk rollback stok
$sqlGet = "SELECT id_barang, jumlah FROM barang_keluar WHERE id_keluar = ?";
$stmtGet = $conn->prepare($sqlGet);
if (!$stmtGet) {
    header('Location: ../data_barang_keluar.php?message=' . urlencode('Gagal menghapus barang keluar'));
    exit;
}
$stmtGet->bind_param("i", $id_keluar);
$stmtGet->execute();
$result = $stmtGet->get_result();
$row = $result->fetch_assoc();
$stmtGet->close();

if (!$row) {
    header('Location: ../data_barang_keluar.php?message=' . urlencode('ID barang keluar tidak valid'));
    exit;
}

$id_barang = $row['id_barang'];
$jumlah    = $row['jumlah'];

// Hapus dan kembalikan stok dalam satu transaksi
$conn->begin_transaction();
try {
    $sqlDel = "DELETE FROM barang_keluar WHERE id_keluar = ?";
    $stmtDel = $conn->prepare($sqlDel);
    $stmtDel->bind_param("i", $id_keluar);
    $stmtDel->execute();
    if ($stmtDel->affected_rows === 0) {
        throw new Exception('Data tidak ditemukan');
    }
    $stmtDel->close();

    // Kembalikan stok karena barang keluar dibatalkan
    $sqlStok = "UPDATE barang SET stok = stok + ? WHERE id_barang = ?";
    $stmtStok = $conn->prepare($sqlStok);
    $stmtStok->bind_param("ii", $jumlah, $id_barang);
    $stmtStok->execute();
    $stmtStok->close();

    $conn->commit();
    header('Location: ../data_barang_keluar.php?message=' . urlencode('Barang keluar berhasil dihapus dan stok telah diperbarui'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../data_barang_keluar.php?message=' . urlencode('Gagal menghapus barang keluar'));
}
exit;
