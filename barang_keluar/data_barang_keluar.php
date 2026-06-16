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
$res = $conn->query("SELECT COUNT(*) AS total FROM barang_keluar");
if ($res) { $totalRows = (int)$res->fetch_assoc()['total']; $res->free(); }
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$dataBarangKeluar = [];
$sql = "SELECT bk.id_keluar, bk.tanggal, bk.jumlah, bk.tujuan,
               b.nama_barang, u.username
        FROM barang_keluar bk
        JOIN barang b ON bk.id_barang = b.id_barang
        LEFT JOIN users u ON bk.id_user = u.id_user
        ORDER BY bk.id_keluar DESC LIMIT ? OFFSET ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) { $dataBarangKeluar[] = $row; }
    $stmt->close();
}

$allowed_messages = [
    'Barang keluar berhasil ditambahkan dan stok telah diperbarui',
    'Barang keluar berhasil diperbarui dan stok telah disesuaikan',
    'Gagal menambahkan barang keluar','Gagal memperbarui barang keluar',
    'Semua field harus diisi dengan benar','Stok barang tidak mencukupi',
    'ID barang keluar tidak valid','Akses tidak sah!',
    'Barang keluar berhasil dihapus dan stok telah diperbarui',
    'Gagal menghapus barang keluar',
];
$message = '';
if (isset($_GET['message']) && in_array($_GET['message'], $allowed_messages)) {
    $message = $_GET['message'];
}
$role       = $_SESSION['role'] ?? '';
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang Keluar</title>
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
                            <h3 class="mb-0">Daftar Barang Keluar</h3>
                            <div class="d-flex gap-2">
                                <a href="tambah_barang_keluar.php" class="btn btn-primary">Tambah Barang Keluar</a>
                                <a href="export_barang_keluar.php" class="btn btn-success">Export Excel</a>
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
                                            <th>Tanggal</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Tujuan</th>
                                            <th>Dicatat Oleh</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($dataBarangKeluar)): ?>
                                            <tr><td colspan="7" class="text-center text-muted">Belum ada data barang keluar.</td></tr>
                                        <?php else: ?>
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
                                                        <form method="POST" action="proses/hapus.php" style="display:inline"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data barang keluar ini? Stok barang akan dikembalikan.')">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                            <input type="hidden" name="id" value="<?php echo intval($item['id_keluar']); ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                        </form>
                                                    </td>
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
