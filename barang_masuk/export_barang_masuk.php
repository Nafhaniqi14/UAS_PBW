<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../index.php'); exit;
}
include '../koneksi_db.php';
require_once '../asset/php/ExcelExport.php';

$rows = [];
$sql  = "SELECT bm.tanggal, b.nama_barang, s.nama_supplier, bm.jumlah, u.username
         FROM barang_masuk bm
         JOIN barang b ON bm.id_barang = b.id_barang
         JOIN supplier s ON bm.id_supplier = s.id_supplier
         LEFT JOIN users u ON bm.id_user = u.id_user
         ORDER BY bm.id_masuk DESC";
if ($result = $conn->query($sql)) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            $no++,
            $row['tanggal'],
            $row['nama_barang'],
            $row['nama_supplier'],
            (int)$row['jumlah'],
            $row['username'] ?? '-',
        ];
    }
    $result->free();
}

$headers  = ['No', 'Tanggal', 'Nama Barang', 'Supplier', 'Jumlah', 'Dicatat Oleh'];
$exporter = new ExcelExport('Barang Masuk', $headers, $rows, 'FF2E7D32');
$exporter->download('barang_masuk_' . date('Ymd_His') . '.xlsx');
