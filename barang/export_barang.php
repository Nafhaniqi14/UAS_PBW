<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../index.php'); exit;
}
include '../koneksi_db.php';
require_once '../asset/php/ExcelExport.php';

$rows = [];
$sql  = "SELECT b.nama_barang, k.nama_kategori, b.stok, b.satuan, b.harga
         FROM barang b
         JOIN kategori k ON b.id_kategori = k.id_kategori
         ORDER BY b.id_barang ASC";
if ($result = $conn->query($sql)) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            $no++,
            $row['nama_barang'],
            $row['nama_kategori'],
            (int)$row['stok'],
            $row['satuan'] ?? '-',
            'Rp ' . number_format($row['harga'], 0, ',', '.'),
        ];
    }
    $result->free();
}

$headers  = ['No', 'Nama Barang', 'Kategori', 'Stok', 'Satuan', 'Harga'];
$exporter = new ExcelExport('Data Barang', $headers, $rows, 'FF4472C4');
$exporter->download('data_barang_' . date('Ymd_His') . '.xlsx');
