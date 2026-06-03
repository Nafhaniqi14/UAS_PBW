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

function getAllSuppliers($conn) {
    $suppliers = [];
    $sql = "SELECT id_supplier, nama_supplier, alamat, no_hp FROM supplier ORDER BY id_supplier ASC";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
        }
        $result->free();
    }
    return $suppliers;
}

$message = '';
if (isset($_GET['message'])) {
    $message = sanitize($_GET['message']);
}

$suppliers = getAllSuppliers($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Supplier</title>
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
                            <h3 class="mb-0">Daftar Supplier</h3>
                            <a href="tambah_supplier.php" class="btn btn-primary">Tambah Supplier</a>
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
                                            <th>Nama Supplier</th>
                                            <th>Alamat</th>
                                            <th>No HP</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <tr>
                                                <td><?php echo $supplier['id_supplier']; ?></td>
                                                <td><?php echo htmlspecialchars($supplier['nama_supplier']); ?></td>
                                                <td><?php echo htmlspecialchars($supplier['alamat']); ?></td>
                                                <td><?php echo htmlspecialchars($supplier['no_hp']); ?></td>
                                                <td>
                                                    <a href="edit_supplier.php?id=<?php echo $supplier['id_supplier']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <a href="delete_supplier.php?id=<?php echo $supplier['id_supplier']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">Hapus</a>
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

