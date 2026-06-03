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

// Ambil daftar barang untuk dropdown
$listBarang = [];
$sqlBarang = "SELECT id_barang, nama_barang, stok FROM barang ORDER BY nama_barang ASC";
if ($result = $conn->query($sqlBarang)) {
    while ($row = $result->fetch_assoc()) {
        $listBarang[] = $row;
    }
    $result->free();
}

$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang Keluar</title>
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
                            <div>
                                <h3>Tambah Barang Keluar</h3>
                                <p class="text-muted">Isi data barang keluar baru. Stok barang akan otomatis berkurang.</p>
                            </div>
                            <a href="data_barang_keluar.php" class="btn btn-secondary">Kembali ke Daftar Barang Keluar</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <form method="POST" action="proses/proses_tambah.php">
                                <!-- CSRF Token -->
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                <div class="mb-3">
                                    <label class="form-label">Tanggal Keluar</label>
                                    <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Barang</label>
                                    <select name="id_barang" class="form-control" required>
                                        <option value="">-- Pilih Barang --</option>
                                        <?php foreach ($listBarang as $barang): ?>
                                            <option value="<?php echo intval($barang['id_barang']); ?>">
                                                <?php echo htmlspecialchars($barang['nama_barang']); ?>
                                                (Stok: <?php echo intval($barang['stok']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah" class="form-control" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tujuan</label>
                                    <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Gudang A, Divisi IT, dll" maxlength="100">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Tambah Barang Keluar</button>
                            </form>
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