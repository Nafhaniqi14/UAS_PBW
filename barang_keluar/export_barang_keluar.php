<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../index.php'); exit;
}
include '../koneksi_db.php';
require_once '../asset/php/ExcelExport.php';

$rows = [];
$sql  = "SELECT bk.tanggal, b.nama_barang, bk.jumlah, bk.tujuan, u.username
         FROM barang_keluar bk
         JOIN barang b ON bk.id_barang = b.id_barang
         LEFT JOIN users u ON bk.id_user = u.id_user
         ORDER BY bk.id_keluar DESC";
if ($result = $conn->query($sql)) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            $no++,
            $row['tanggal'],
            $row['nama_barang'],
            (int)$row['jumlah'],
            $row['tujuan'] ?? '-',
            $row['username'] ?? '-',
        ];
    }
    $result->free();
}

$headers  = ['No', 'Tanggal', 'Nama Barang', 'Jumlah', 'Tujuan', 'Dicatat Oleh'];
$exporter = new ExcelExport('Barang Keluar', $headers, $rows, 'FFC62828');
$exporter->download('barang_keluar_' . date('Ymd_His') . '.xlsx');
