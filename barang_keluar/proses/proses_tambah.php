<?php
session_start();
if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true) {
    header('Location: ../../index.php');
    exit;
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data_barang_keluar.php?message=Akses tidak sah!');
    exit;
}

// Validasi CSRF token
if (!isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../data_barang_keluar.php?message=Akses tidak sah!');
    exit;
}

include '../../koneksi_db.php';

$tanggal   = isset($_POST['tanggal'])    ? trim($_POST['tanggal'])     : '';
$id_barang = isset($_POST['id_barang'])  ? intval($_POST['id_barang']) : 0;
$jumlah    = isset($_POST['jumlah'])     ? intval($_POST['jumlah'])    : 0;
$tujuan    = isset($_POST['tujuan'])     ? trim($_POST['tujuan'])      : '';
$id_user   = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null;

// Batasi panjang string
$tanggal = substr($tanggal, 0, 10);
$tujuan  = substr($tujuan, 0, 100);

// Validasi input dasar
if (empty($tanggal) || $id_barang <= 0 || $jumlah <= 0) {
    header('Location: ../data_barang_keluar.php?message=Semua field harus diisi dengan benar');
    exit;
}

// Validasi format tanggal (harus Y-m-d)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    header('Location: ../data_barang_keluar.php?message=Semua field harus diisi dengan benar');
    exit;
}

$conn->begin_transaction();

$stokSaat = 0;
$sqlCekStok = "SELECT stok FROM barang WHERE id_barang = ? FOR UPDATE";
if ($stmtCek = $conn->prepare($sqlCekStok)) {
    $stmtCek->bind_param("i", $id_barang);
    $stmtCek->execute();
    $result = $stmtCek->get_result();
    if ($row = $result->fetch_assoc()) {
        $stokSaat = $row['stok'];
    } else {
        // id_barang tidak ditemukan di tabel barang
        $conn->rollback();
        header('Location: ../data_barang_keluar.php?message=Semua field harus diisi dengan benar');
        exit;
    }
    $stmtCek->close();
} else {
    $conn->rollback();
    header('Location: ../data_barang_keluar.php?message=Gagal menambahkan barang keluar');
    exit;
}

if ($jumlah > $stokSaat) {
    $conn->rollback();
    header('Location: ../data_barang_keluar.php?message=Stok barang tidak mencukupi');
    exit;
}

// INSERT ke tabel barang_keluar
$sql = "INSERT INTO barang_keluar (id_barang, tanggal, jumlah, tujuan, id_user) VALUES (?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("isisi", $id_barang, $tanggal, $jumlah, $tujuan, $id_user);
    if ($stmt->execute()) {
        $stmt->close();

        // Kurangi stok barang otomatis
        $sqlStok = "UPDATE barang SET stok = stok - ? WHERE id_barang = ?";
        if ($stmtStok = $conn->prepare($sqlStok)) {
            $stmtStok->bind_param("ii", $jumlah, $id_barang);
            if ($stmtStok->execute()) {
                $stmtStok->close();
                $conn->commit();
                header('Location: ../data_barang_keluar.php?message=Barang keluar berhasil ditambahkan dan stok telah diperbarui');
            } else {
                $stmtStok->close();
                $conn->rollback();
                header('Location: ../data_barang_keluar.php?message=Gagal menambahkan barang keluar');
            }
        } else {
            $conn->rollback();
            header('Location: ../data_barang_keluar.php?message=Gagal menambahkan barang keluar');
        }
    } else {
        $stmt->close();
        $conn->rollback();
        header('Location: ../data_barang_keluar.php?message=Gagal menambahkan barang keluar');
    }
} else {
    $conn->rollback();
    header('Location: ../data_barang_keluar.php?message=Gagal menambahkan barang keluar');
}
?>