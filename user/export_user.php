<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php'); exit;
}
include '../koneksi_db.php';
require_once '../asset/php/ExcelExport.php';

$rows = [];
if ($result = $conn->query("SELECT username, role, status FROM users ORDER BY id_user ASC")) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $rows[] = [$no++, $row['username'], $row['role'], $row['status']];
    }
    $result->free();
}

$headers  = ['No', 'Username', 'Role', 'Status'];
$exporter = new ExcelExport('Data User', $headers, $rows, 'FF1565C0');
$exporter->download('data_user_' . date('Ymd_His') . '.xlsx');
