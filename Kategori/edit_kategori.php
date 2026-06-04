<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: data_kategori.php?message=' . urlencode('ID kategori tidak valid'));
    exit;
}

$id_kategori = intval($_GET['id']);
include '../koneksi_db.php';

$sql = "SELECT id_kategori, nama_kategori FROM kategori WHERE id_kategori = ? LIMIT 1";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('i', $id_kategori);
    $stmt->execute();
    $result = $stmt->get_result();
    $kategori = $result->fetch_assoc();
    $stmt->close();
} else {
    $kategori = null;
}

if (!$kategori) {
    header('Location: data_kategori.php?message=' . urlencode('ID kategori tidak valid'));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Kategori</title>
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
                                <h3>Edit Kategori</h3>
                                <p class="text-muted">Ubah nama kategori barang sesuai kebutuhan.</p>
                            </div>
                            <a href="data_kategori.php" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <form method="POST" action="proses/proses_kategori.php">
                                <input type="hidden" name="id_kategori" value="<?php echo intval($kategori['id_kategori']); ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nama Kategori</label>
                                    <input type="text" name="nama_kategori" class="form-control" value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
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
