<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

include '../koneksi_db.php';

function getAllKategori($conn) {
    $kategori = [];
    $sql = "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $kategori[] = $row;
        }
        $result->free();
    }
    return $kategori;
}

$allowed_messages = [
    'Kategori berhasil ditambahkan',
    'Kategori berhasil diperbarui',
    'Kategori berhasil dihapus',
    'Gagal menambahkan kategori',
    'Gagal memperbarui kategori',
    'Gagal menghapus kategori',
    'Gagal menghapus kategori karena masih digunakan',
    'Semua field harus diisi',
    'ID kategori tidak valid',
];

$message = '';
if (isset($_GET['message']) && in_array($_GET['message'], $allowed_messages, true)) {
    $message = $_GET['message'];
}

$dataKategori = getAllKategori($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Kategori</title>
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
                            <h3 class="mb-0">Daftar Kategori</h3>
                            <a href="tambah_kategori.php" class="btn btn-primary">Tambah Kategori</a>
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
                                            <th>Nama Kategori</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dataKategori as $kategori): ?>
                                            <tr>
                                                <td><?php echo intval($kategori['id_kategori']); ?></td>
                                                <td><?php echo htmlspecialchars($kategori['nama_kategori']); ?></td>
                                                <td>
                                                    <a href="edit_kategori.php?id=<?php echo intval($kategori['id_kategori']); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <a href="hapus_kategori.php?id=<?php echo intval($kategori['id_kategori']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">Hapus</a>
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
