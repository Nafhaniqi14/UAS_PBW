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

$perPage = 10;
$page    = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset  = ($page - 1) * $perPage;

$totalRows = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM barang");
if ($res) { $totalRows = (int)$res->fetch_assoc()['total']; $res->free(); }
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$dataBarang = [];
$sql = "SELECT b.id_barang, b.nama_barang, b.stok, b.satuan, b.harga, k.nama_kategori
        FROM barang b
        JOIN kategori k ON b.id_kategori = k.id_kategori
        ORDER BY b.id_barang ASC LIMIT ? OFFSET ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) { $dataBarang[] = $row; }
    $stmt->close();
}

$allowed_messages = [
    'Barang berhasil ditambahkan','Barang berhasil diperbarui','Barang berhasil dihapus',
    'Gagal menambahkan barang','Gagal memperbarui barang','Gagal menghapus barang',
    'ID barang tidak valid','Semua field harus diisi dengan benar','Akses tidak sah!',
];
$message = '';
if (isset($_GET['message']) && in_array($_GET['message'], $allowed_messages)) {
    $message = $_GET['message'];
}
$role        = $_SESSION['role'] ?? '';
$csrf_token  = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="icon" href="../asset/img/logo_website.png" type="image/x-icon" />
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
                            <div class="d-flex gap-2">
                                <?php if ($role === 'admin'): ?>
                                    <a href="tambah_barang.php" class="btn btn-primary">Tambah Barang</a>
                                <?php endif; ?>
                                <a href="export_barang.php" class="btn btn-success">Export Excel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($message): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                                            <?php if ($role === 'admin'): ?><th>Aksi</th><?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($dataBarang)): ?>
                                            <tr><td colspan="7" class="text-center text-muted">Belum ada data barang.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($dataBarang as $barang): ?>
                                                <tr class="<?php echo (intval($barang['stok']) <= 5) ? 'table-warning' : ''; ?>">
                                                    <td><?php echo intval($barang['id_barang']); ?></td>
                                                    <td><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                                                    <td><?php echo htmlspecialchars($barang['nama_kategori']); ?></td>
                                                    <td>
                                                        <?php echo intval($barang['stok']); ?>
                                                        <?php if (intval($barang['stok']) <= 5): ?>
                                                            <span class="badge bg-warning text-dark ms-1">Stok Rendah</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($barang['satuan'] ?? '-'); ?></td>
                                                    <td>Rp <?php echo number_format($barang['harga'], 0, ',', '.'); ?></td>
                                                    <?php if ($role === 'admin'): ?>
                                                        <td>
                                                            <a href="edit_barang.php?id=<?php echo intval($barang['id_barang']); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
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
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($totalPages > 1): ?>
                            <nav class="mt-3">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Sebelumnya</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Berikutnya</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>
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
