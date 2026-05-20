<?php
session_start();

if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true) {
    header('Location: index.php');
    exit;
}

include 'koneksi_db.php';

function getCount($conn, $table) {
    $count = 0;
    $sql = "SELECT COUNT(*) AS total FROM `$table`";
    if ($result = $conn->query($sql)) {
        $row = $result->fetch_assoc();
        $count = isset($row['total']) ? (int) $row['total'] : 0;
        $result->free();
    }
    return $count;
}

$totalBarang = getCount($conn, 'barang');
$totalMasuk = getCount($conn, 'barang_masuk');
$totalKeluar = getCount($conn, 'barang_keluar');
$totalKategori = getCount($conn, 'kategori');
$totalSupplier = getCount($conn, 'supplier');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body class="admin-dashboard">

<div class="sidebar d-none d-md-block">
    <?php include 'layout/sidebar.php'; ?>
</div>

<div class="main-wrapper">
    <?php include 'layout/nav.php'; ?>

    <div class="content">
        <div class="container-fluid">

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body">
                        <h3>
                            Selamat Datang,
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </h3>

                        <p class="text-muted">
                            Sistem Inventaris Barang Berbasis Web
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card card-dashboard bg-primary text-white shadow">
                    <div class="card-body text-center">
                        <h5>Total Barang</h5>
                        <h2><?php echo $totalBarang; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard bg-success text-white shadow">
                    <div class="card-body text-center">
                        <h5>Barang Masuk</h5>
                        <h2><?php echo $totalMasuk; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard bg-warning text-white shadow">
                    <div class="card-body text-center">
                        <h5>Barang Keluar</h5>
                        <h2><?php echo $totalKeluar; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard bg-info text-white shadow">
                    <div class="card-body text-center">
                        <h5>Kategori</h5>
                        <h2><?php echo $totalKategori; ?></h2>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-dashboard bg-secondary text-white shadow">
                    <div class="card-body text-center">
                        <h5>Supplier</h5>
                        <h2><?php echo $totalSupplier; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="asset/js/main.js"></script>
</body>
</html>