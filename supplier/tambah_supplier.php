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

$message = '';
$allowed_messages = ['Semua field harus diisi', 'Nomor HP hanya boleh berisi angka dan tanda + atau -', 'Gagal menambahkan supplier'];
if (isset($_GET['message']) && in_array($_GET['message'], $allowed_messages, true)) {
    $message = $_GET['message'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Supplier</title>
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
                                <h3>Tambah Supplier</h3>
                                <p class="text-muted mb-0">Isi data supplier baru untuk ditambahkan ke sistem.</p>
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
                            <form method="POST" action="proses/proses_tambah_supplier.php">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nama Supplier</label>
                                    <input type="text" name="nama_supplier" class="form-control" placeholder="Masukkan nama supplier" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat supplier" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No HP</label>
                                    <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789" pattern="^[\d\+\-]{7,20}$" title="Nomor HP hanya boleh berisi angka, tanda + atau -, minimal 7 karakter" required>
                                    <div class="form-text">Hanya angka, tanda + atau -, contoh: 08123456789 atau +628123456789</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">Tambah Supplier</button>
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
