<?php
session_start();

if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../index.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../koneksi_db.php';

function getAllBarang($conn) {
    $data = [];
    $sql = "SELECT b.id_barang, b.nama_barang, b.stok, b.satuan, b.harga, k.nama_kategori
            FROM barang b
            JOIN kategori k ON b.id_kategori = k.id_kategori
            ORDER BY b.id_barang ASC";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $result->free();
    }
    return $data;
}

// Whitelist pesan yang diizinkan
$allowed_messages = [
    'Barang berhasil ditambahkan',
    'Barang berhasil diperbarui',
    'Barang berhasil dihapus',
    'Gagal menambahkan barang',
    'Gagal memperbarui barang',
    'Gagal menghapus barang',
    'ID barang tidak valid',
    'Semua field harus diisi dengan benar',
    'Akses tidak sah!',
];

$message = '';
if (isset($_GET['message']) && in_array($_GET['message'], $allowed_messages)) {
    $message = $_GET['message'];
}

$dataBarang = getAllBarang($conn);
$role = $_SESSION['role'] ?? '';
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
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
                            <h3 class="mb-0">Daftar Barang</h3>
                            <?php if ($role === 'admin'): ?>
                                <a href="tambah_barang.php" class="btn btn-primary">Tambah Barang</a>
                            <?php endif; ?>
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
                                            <th>Nama Barang</th>
                                            <th>Kategori</th>
                                            <th>Stok</th>
                                            <th>Satuan</th>
                                            <th>Harga</th>
                                            <?php if ($role === 'admin'): ?>
                                                <th>Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataBarang as $barang): ?>
                                            <tr>
                                                <td><?php echo intval($barang['id_barang']); ?></td>
                                                <td><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                                                <td><?php echo htmlspecialchars($barang['nama_kategori']); ?></td>
                                                <td><?php echo htmlspecialchars($barang['stok']); ?></td>
                                                <td><?php echo htmlspecialchars($barang['satuan'] ?? '-'); ?></td>
                                                <td>Rp <?php echo number_format($barang['harga'], 0, ',', '.'); ?></td>
                                                <?php if ($role === 'admin'): ?>
                                                    <td>
                                                        <!-- Tombol Edit: pakai GET biasa (hanya membuka form, tidak mengubah data) -->
                                                        <a href="edit_barang.php?id=<?php echo intval($barang['id_barang']); ?>" class="btn btn-sm btn-outline-primary">Edit</a>

                                                        <!-- Tombol Hapus: pakai POST + CSRF token -->
                                                        <form method="POST" action="proses/proses_delete.php" style="display:inline"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                            <input type="hidden" name="id" value="<?php echo intval($barang['id_barang']); ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                        </form>
                                                    </td>
                                                <?php endif; ?>
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