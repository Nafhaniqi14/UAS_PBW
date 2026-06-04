<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

include '../../koneksi_db.php';

$id_masuk       = isset($_POST['id_masuk'])       ? intval($_POST['id_masuk'])       : 0;
$tanggal        = isset($_POST['tanggal'])         ? trim($_POST['tanggal'])          : '';
$id_barang      = isset($_POST['id_barang'])       ? intval($_POST['id_barang'])      : 0;
$id_supplier    = isset($_POST['id_supplier'])     ? intval($_POST['id_supplier'])    : 0;
$jumlah         = isset($_POST['jumlah'])          ? intval($_POST['jumlah'])         : 0;
$id_barang_lama = isset($_POST['id_barang_lama'])  ? intval($_POST['id_barang_lama']) : 0;
$jumlah_lama    = isset($_POST['jumlah_lama'])     ? intval($_POST['jumlah_lama'])    : 0;

if ($id_masuk <= 0 || empty($tanggal) || $id_barang <= 0 || $id_supplier <= 0 || $jumlah <= 0) {
    header('Location: ../data_barang_masuk.php?message=Semua field harus diisi dengan benar');
    exit;
}

// Update data barang masuk
$sql = "UPDATE barang_masuk SET tanggal = ?, id_barang = ?, id_supplier = ?, jumlah = ? WHERE id_masuk = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("siiii", $tanggal, $id_barang, $id_supplier, $jumlah, $id_masuk);
    if ($stmt->execute()) {
        // Kembalikan stok barang lama
        $sqlKembalikan = "UPDATE barang SET stok = stok - ? WHERE id_barang = ?";
        if ($stmtK = $conn->prepare($sqlKembalikan)) {
            $stmtK->bind_param("ii", $jumlah_lama, $id_barang_lama);
            $stmtK->execute();
            $stmtK->close();
        }
        // Tambahkan stok barang baru
        $sqlTambah = "UPDATE barang SET stok = stok + ? WHERE id_barang = ?";
        if ($stmtT = $conn->prepare($sqlTambah)) {
            $stmtT->bind_param("ii", $jumlah, $id_barang);
            $stmtT->execute();
            $stmtT->close();
        }
        header('Location: ../data_barang_masuk.php?message=Barang masuk berhasil diperbarui dan stok telah disesuaikan');
    } else {
        header('Location: ../data_barang_masuk.php?message=Gagal memperbarui barang masuk');
    }
    $stmt->close();
} else {
    header('Location: ../data_barang_masuk.php?message=Error: ' . $conn->error);
}
?>
