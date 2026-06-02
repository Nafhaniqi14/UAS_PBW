<?php
session_start();
if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true) {
    header('Location: ../index.php');
    exit;
}
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: data_barang.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../koneksi_db.php';

$listKategori = [];
$sqlKategori = "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC";
if ($result = $conn->query($sqlKategori)) {
    while ($row = $result->fetch_assoc()) {
        $listKategori[] = $row;
    }
    $result->free();
}

$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
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
                                <h3>Tambah Barang</h3>
                                <p class="text-muted">Isi data barang baru untuk ditambahkan ke sistem.</p>
                            </div>
                            <a href="data_barang.php" class="btn btn-secondary">Kembali ke Daftar Barang</a>
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
                                    <label class="form-label">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="id_kategori" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php foreach ($listKategori as $kategori): ?>
                                            <option value="<?php echo intval($kategori['id_kategori']); ?>">
                                                <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stok Awal</label>
                                    <input type="number" name="stok" class="form-control" min="0" value="0" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" name="satuan" class="form-control" placeholder="Contoh: pcs, kg, liter" maxlength="50">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Harga</label>
                                    <input type="number" name="harga" class="form-control" min="0" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Tambah Barang</button>
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