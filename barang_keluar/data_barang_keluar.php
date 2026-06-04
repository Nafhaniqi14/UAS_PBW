<?php
session_start();

if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Generate CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../koneksi_db.php';

function getAllBarangKeluar($conn) {
    $data = [];
    $sql = "SELECT bk.id_keluar, bk.tanggal, bk.jumlah, bk.tujuan,
                   b.nama_barang, u.username
            FROM barang_keluar bk
            JOIN barang b ON bk.id_barang = b.id_barang
            LEFT JOIN users u ON bk.id_user = u.id_user
            ORDER BY bk.id_keluar DESC";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $result->free();
    }
    return $data;
}

$allowed_messages = [
    'Barang keluar berhasil ditambahkan dan stok telah diperbarui',
    'Barang keluar berhasil diperbarui dan stok telah disesuaikan',
    'Gagal menambahkan barang keluar',
    'Gagal memperbarui barang keluar',
    'Semua field harus diisi dengan benar',
    'Stok barang tidak mencukupi',
    'ID barang keluar tidak valid',
    'Akses tidak sah!',
];

$message = '';
if (isset($_GET['message']) && in_array($_GET['message'], $allowed_messages)) {
    $message = $_GET['message'];
}

$dataBarangKeluar = getAllBarangKeluar($conn);
$role = $_SESSION['role'] ?? '';
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang Keluar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/css/style.css">
</head>
<body class="admin-dashboard">

<div class="sidebar d-none d-md-block">
    <?php include '../layout/sidebar.php'; ?>
</div>

<div class="main-wrapper">
    <?php include '../layout/nav.php'; ?>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Daftar Barang Keluar</h3>
                            <a href="tambah_barang_keluar.php" class="btn btn-primary">Tambah Barang Keluar</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <?php echo htmlspecialchars($message); ?>
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
                                            <th>Jumlah</th>
                                            <th>Tujuan</th>
                                            <th>Dicatat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataBarangKeluar as $item): ?>
                                            <tr>
                                                <td><?php echo intval($item['id_keluar']); ?></td>
                                                <td><?php echo htmlspecialchars($item['tanggal']); ?></td>
                                                <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                                <td><?php echo intval($item['jumlah']); ?></td>
                                                <td><?php echo htmlspecialchars($item['tujuan'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($item['username'] ?? '-'); ?></td>
                                                <td>
                                                    <a href="edit_barang_keluar.php?id=<?php echo intval($item['id_keluar']); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
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