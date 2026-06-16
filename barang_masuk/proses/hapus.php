<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data_barang_masuk.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

if (!isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../data_barang_masuk.php?message=' . urlencode('Akses tidak sah!'));
    exit;
}

include '../../koneksi_db.php';

$id_masuk = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_masuk <= 0) {
    header('Location: ../data_barang_masuk.php?message=' . urlencode('ID barang masuk tidak valid'));
    exit;
}

// Ambil data barang masuk untuk rollback stok
$sqlGet = "SELECT id_barang, jumlah FROM barang_masuk WHERE id_masuk = ?";
$stmtGet = $conn->prepare($sqlGet);
if (!$stmtGet) {
    header('Location: ../data_barang_masuk.php?message=' . urlencode('Gagal menghapus barang masuk'));
    exit;
}
$stmtGet->bind_param("i", $id_masuk);
$stmtGet->execute();
$result = $stmtGet->get_result();
$row = $result->fetch_assoc();
$stmtGet->close();

if (!$row) {
    header('Location: ../data_barang_masuk.php?message=' . urlencode('ID barang masuk tidak valid'));
    exit;
}

$id_barang = $row['id_barang'];
$jumlah    = $row['jumlah'];

// Hapus dan rollback stok dalam satu transaksi
$conn->begin_transaction();
try {
    $sqlDel = "DELETE FROM barang_masuk WHERE id_masuk = ?";
    $stmtDel = $conn->prepare($sqlDel);
    $stmtDel->bind_param("i", $id_masuk);
    $stmtDel->execute();
    if ($stmtDel->affected_rows === 0) {
        throw new Exception('Data tidak ditemukan');
    }
    $stmtDel->close();

    // Kurangi stok kembali karena barang masuk dibatalkan
    $sqlStok = "UPDATE barang SET stok = stok - ? WHERE id_barang = ?";
    $stmtStok = $conn->prepare($sqlStok);
    $stmtStok->bind_param("ii", $jumlah, $id_barang);
    $stmtStok->execute();
    $stmtStok->close();

    $conn->commit();
    header('Location: ../data_barang_masuk.php?message=' . urlencode('Barang masuk berhasil dihapus dan stok telah diperbarui'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../data_barang_masuk.php?message=' . urlencode('Gagal menghapus barang masuk'));
}
exit;
