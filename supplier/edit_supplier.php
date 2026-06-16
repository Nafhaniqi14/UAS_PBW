<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

include '../koneksi_db.php';

$id_supplier = isset($_GET['id']) ? intval($_GET['id']) : 0;
$supplier = [];

if ($id_supplier > 0) {
    $sql = "SELECT id_supplier, nama_supplier, alamat, no_hp FROM supplier WHERE id_supplier = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_supplier);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $supplier = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

if (empty($supplier)) {
    header('Location: data_supplier.php?message=' . urlencode('Supplier tidak ditemukan'));
    exit;
}

$message = '';
$allowed_messages = ['Semua field harus diisi', 'Nomor HP hanya boleh berisi angka dan tanda + atau -', 'Gagal memperbarui supplier'];
if (isset($_GET['message']) && in_array($_GET['message'], $allowed_messages, true)) {
    $message = $_GET['message'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Supplier</title>
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
                            <div>
                                <h3>Edit Supplier</h3>
                                <p class="text-muted mb-0">Edit data supplier yang sudah terdaftar.</p>
                            </div>
                            <a href="data_supplier.php" class="btn btn-secondary">Kembali ke Daftar Supplier</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($message): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <div class="row g-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body">
                            <form method="POST" action="proses/proses_edit_supplier.php">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="id_supplier" value="<?php echo intval($supplier['id_supplier']); ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nama Supplier</label>
                                    <input type="text" name="nama_supplier" class="form-control" value="<?php echo htmlspecialchars($supplier['nama_supplier']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3" required><?php echo htmlspecialchars($supplier['alamat']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No HP</label>
                                    <input type="text" name="no_hp" class="form-control" value="<?php echo htmlspecialchars($supplier['no_hp']); ?>" pattern="^[\d\+\-]{7,20}$" title="Nomor HP hanya boleh berisi angka, tanda + atau -, minimal 7 karakter" required>
                                    <div class="form-text">Hanya angka, tanda + atau -, contoh: 08123456789</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Update Supplier</button>
                                    <a href="data_supplier.php" class="btn btn-outline-secondary w-100">Batal</a>
                                </div>
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
