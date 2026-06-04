<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

include '../koneksi_db.php';

function sanitize($value) {
    return htmlspecialchars(trim($value));
}

function getAllBarangMasuk($conn) {
    $data = [];
    $sql = "SELECT bm.id_masuk, bm.tanggal, bm.jumlah,
                   b.nama_barang, s.nama_supplier, u.username
            FROM barang_masuk bm
            JOIN barang b ON bm.id_barang = b.id_barang
            JOIN supplier s ON bm.id_supplier = s.id_supplier
            LEFT JOIN users u ON bm.id_user = u.id_user
            ORDER BY bm.id_masuk DESC";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $result->free();
    }
    return $data;
}

$message = '';
if (isset($_GET['message'])) {
    $message = sanitize($_GET['message']);
}

$dataBarangMasuk = getAllBarangMasuk($conn);
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/css/style.css">
</head>
<body class="admin-dashboard">

<div class="sidebar d-none d-md-block">
    <?php include '../layout/sidebar_sub.php'; ?>
</div>

<div class="main-wrapper">
    <?php include '../layout/nav_sub.php'; ?>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Daftar Barang Masuk</h3>
                            <a href="tambah_barang_masuk.php" class="btn btn-primary">Tambah Barang Masuk</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <?php echo $message; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tanggal</th>
                                            <th>Nama Barang</th>
                                            <th>Supplier</th>
                                            <th>Jumlah</th>
                                            <th>Dicatat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataBarangMasuk as $item): ?>
                                            <tr>
                                                <td><?php echo $item['id_masuk']; ?></td>
                                                <td><?php echo htmlspecialchars($item['tanggal']); ?></td>
                                                <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                                <td><?php echo htmlspecialchars($item['nama_supplier']); ?></td>
                                                <td><?php echo htmlspecialchars($item['jumlah']); ?></td>
                                                <td><?php echo htmlspecialchars($item['username'] ?? '-'); ?></td>
                                                <td>
                                                    <a href="edit_barang_masuk.php?id=<?php echo $item['id_masuk']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../asset/js/main.js"></script>
</body>
</html>
