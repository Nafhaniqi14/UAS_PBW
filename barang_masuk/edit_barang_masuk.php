<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true) {
    header('Location: ../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

include '../koneksi_db.php';

$id_masuk = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item = [];

if ($id_masuk > 0) {
    $sql = "SELECT id_masuk, tanggal, id_barang, id_supplier, jumlah
            FROM barang_masuk WHERE id_masuk = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_masuk);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $item = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

if (empty($item)) {
    header('Location: data_barang_masuk.php');
    exit;
}

// Ambil daftar barang untuk dropdown
$listBarang = [];
$sqlBarang = "SELECT id_barang, nama_barang, stok FROM barang ORDER BY nama_barang ASC";
if ($result = $conn->query($sqlBarang)) {
    while ($row = $result->fetch_assoc()) {
        $listBarang[] = $row;
    }
    $result->free();
}

// Ambil daftar supplier untuk dropdown
$listSupplier = [];
$sqlSupplier = "SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier ASC";
if ($result = $conn->query($sqlSupplier)) {
    while ($row = $result->fetch_assoc()) {
        $listSupplier[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang Masuk</title>
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
                                <h3>Edit Barang Masuk</h3>
                                <p class="text-muted">Edit data barang masuk. Stok barang akan disesuaikan otomatis.</p>
                            </div>
                            <a href="data_barang_masuk.php" class="btn btn-secondary">Kembali ke Daftar Barang Masuk</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <form method="POST" action="proses/edit.php">
                                <input type="hidden" name="id_masuk" value="<?php echo $item['id_masuk']; ?>">
                                <input type="hidden" name="id_barang_lama" value="<?php echo $item['id_barang']; ?>">
                                <input type="hidden" name="jumlah_lama" value="<?php echo $item['jumlah']; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Masuk</label>
                                    <input type="date" name="tanggal" class="form-control" value="<?php echo htmlspecialchars($item['tanggal']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Barang</label>
                                    <select name="id_barang" class="form-control" required>
                                        <option value="">-- Pilih Barang --</option>
                                        <?php foreach ($listBarang as $barang): ?>
                                            <option value="<?php echo $barang['id_barang']; ?>"
                                                <?php echo ($barang['id_barang'] == $item['id_barang']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($barang['nama_barang']); ?>
                                                (Stok: <?php echo $barang['stok']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Supplier</label>
                                    <select name="id_supplier" class="form-control" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        <?php foreach ($listSupplier as $supplier): ?>
                                            <option value="<?php echo $supplier['id_supplier']; ?>"
                                                <?php echo ($supplier['id_supplier'] == $item['id_supplier']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($supplier['nama_supplier']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="jumlah" class="form-control" min="1" value="<?php echo htmlspecialchars($item['jumlah']); ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Update Barang Masuk</button>
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
