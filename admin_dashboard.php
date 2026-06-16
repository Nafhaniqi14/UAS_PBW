<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}
include 'koneksi_db.php';

function getCount($conn, $table) {
    $count = 0;
    $sql = "SELECT COUNT(*) AS total FROM `$table`";
    if ($result = $conn->query($sql)) {
        $row = $result->fetch_assoc();
        $count = isset($row['total']) ? (int)$row['total'] : 0;
        $result->free();
    }
    return $count;
}

$totalBarang   = getCount($conn, 'barang');
$totalMasuk    = getCount($conn, 'barang_masuk');
$totalKeluar   = getCount($conn, 'barang_keluar');
$totalKategori = getCount($conn, 'kategori');
$totalSupplier = getCount($conn, 'supplier');

// Ambil barang dengan stok rendah (<= 5)
$stokRendah = [];
$sqlStok = "SELECT b.id_barang, b.nama_barang, b.stok, b.satuan, k.nama_kategori
            FROM barang b
            JOIN kategori k ON b.id_kategori = k.id_kategori
            WHERE b.stok <= 5
            ORDER BY b.stok ASC";
if ($result = $conn->query($sqlStok)) {
    while ($row = $result->fetch_assoc()) { $stokRendah[] = $row; }
    $result->free();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/css/style.css">
    <link rel="icon" href="asset/img/logo_website.png" type="image/x-icon" />
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
                            <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                            <p class="text-muted mb-0">Sistem Inventaris Barang Berbasis Web</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
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

            <!-- Tabel Stok Rendah -->
            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <h5 class="mb-3">
                                ⚠️ Barang dengan Stok Rendah
                                <span class="badge bg-warning text-dark ms-2"><?php echo count($stokRendah); ?> item</span>
                            </h5>
                            <?php if (empty($stokRendah)): ?>
                                <p class="text-success mb-0">✅ Semua stok barang dalam kondisi aman.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-warning">
                                            <tr>
                                                <th>Nama Barang</th>
                                                <th>Kategori</th>
                                                <th>Stok</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stokRendah as $barang): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                                                    <td><?php echo htmlspecialchars($barang['nama_kategori']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo intval($barang['stok']) === 0 ? 'danger' : 'warning text-dark'; ?>">
                                                            <?php echo intval($barang['stok']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($barang['satuan'] ?? '-'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
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
