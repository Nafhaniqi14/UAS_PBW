<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php'); exit;
}
include '../koneksi_db.php';
require_once '../asset/php/ExcelExport.php';

$rows = [];
$sql  = "SELECT nama_supplier, alamat, no_hp FROM supplier ORDER BY id_supplier ASC";
if ($result = $conn->query($sql)) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [$no++, $row['nama_supplier'], $row['alamat'], $row['no_hp']];
    }
    $result->free();
}

$headers  = ['No', 'Nama Supplier', 'Alamat', 'No HP'];
$exporter = new ExcelExport('Data Supplier', $headers, $rows, 'FF2E7D32');
$exporter->download('data_supplier_' . date('Ymd_His') . '.xlsx');
