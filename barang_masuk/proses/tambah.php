<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

include '../../koneksi_db.php';

$tanggal     = isset($_POST['tanggal'])     ? trim($_POST['tanggal'])       : '';
$id_barang   = isset($_POST['id_barang'])   ? intval($_POST['id_barang'])   : 0;
$id_supplier = isset($_POST['id_supplier']) ? intval($_POST['id_supplier']) : 0;
$jumlah      = isset($_POST['jumlah'])      ? intval($_POST['jumlah'])      : 0;
$id_user     = isset($_SESSION['id_user'])  ? intval($_SESSION['id_user'])  : null;

if (empty($tanggal) || $id_barang <= 0 || $id_supplier <= 0 || $jumlah <= 0) {
    header('Location: ../data_barang_masuk.php?message=' . urlencode('Semua field harus diisi dengan benar'));
    exit;
}

// Gunakan transaksi agar INSERT dan UPDATE stok selalu konsisten
$conn->begin_transaction();
try {
    $sql = "INSERT INTO barang_masuk (id_barang, id_supplier, tanggal, jumlah, id_user) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisii", $id_barang, $id_supplier, $tanggal, $jumlah, $id_user);
    $stmt->execute();
    $stmt->close();

    $sqlStok = "UPDATE barang SET stok = stok + ? WHERE id_barang = ?";
    $stmtStok = $conn->prepare($sqlStok);
    $stmtStok->bind_param("ii", $jumlah, $id_barang);
    $stmtStok->execute();
    if ($stmtStok->affected_rows === 0) {
        throw new Exception('Barang tidak ditemukan');
    }
    $stmtStok->close();

    $conn->commit();
    header('Location: ../data_barang_masuk.php?message=' . urlencode('Barang masuk berhasil ditambahkan dan stok telah diperbarui'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../data_barang_masuk.php?message=' . urlencode('Gagal menambahkan barang masuk: ' . $e->getMessage()));
}
exit;
