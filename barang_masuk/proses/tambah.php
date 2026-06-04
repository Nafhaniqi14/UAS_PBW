<?php
session_start();
if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true) {
    header('Location: ../../index.php');
    exit;
}

include '../../koneksi_db.php';

$tanggal     = isset($_POST['tanggal'])     ? trim($_POST['tanggal'])      : '';
$id_barang   = isset($_POST['id_barang'])   ? intval($_POST['id_barang'])  : 0;
$id_supplier = isset($_POST['id_supplier']) ? intval($_POST['id_supplier']): 0;
$jumlah      = isset($_POST['jumlah'])      ? intval($_POST['jumlah'])     : 0;
$id_user     = isset($_SESSION['id_user'])  ? intval($_SESSION['id_user']) : null;

if (empty($tanggal) || $id_barang <= 0 || $id_supplier <= 0 || $jumlah <= 0) {
    header('Location: ../data_barang_masuk.php?message=Semua field harus diisi dengan benar');
    exit;
}

// INSERT ke tabel barang_masuk
$sql = "INSERT INTO barang_masuk (id_barang, id_supplier, tanggal, jumlah, id_user) VALUES (?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("iisii", $id_barang, $id_supplier, $tanggal, $jumlah, $id_user);
    if ($stmt->execute()) {
        // Update stok barang otomatis bertambah
        $sqlStok = "UPDATE barang SET stok = stok + ? WHERE id_barang = ?";
        if ($stmtStok = $conn->prepare($sqlStok)) {
            $stmtStok->bind_param("ii", $jumlah, $id_barang);
            $stmtStok->execute();
            $stmtStok->close();
        }
        header('Location: ../data_barang_masuk.php?message=Barang masuk berhasil ditambahkan dan stok telah diperbarui');
    } else {
        header('Location: ../data_barang_masuk.php?message=Gagal menambahkan barang masuk');
    }
    $stmt->close();
} else {
    header('Location: ../data_barang_masuk.php?message=Error: ' . $conn->error);
}
?>
