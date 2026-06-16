<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php'); exit;
}
include '../koneksi_db.php';
require_once '../asset/php/ExcelExport.php';

$rows = [];
if ($result = $conn->query("SELECT nama_kategori FROM kategori ORDER BY nama_kategori ASC")) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [$no++, $row['nama_kategori']];
    }
    $result->free();
}

$headers  = ['No', 'Nama Kategori'];
$exporter = new ExcelExport('Data Kategori', $headers, $rows, 'FF6A1E77');
$exporter->download('data_kategori_' . date('Ymd_His') . '.xlsx');
