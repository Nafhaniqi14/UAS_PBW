<?php
session_start();

if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

include 'koneksi_db.php';

function sanitize($value) {
    return htmlspecialchars(trim($value));
}

function getAllUsers($conn) {
    $users = [];
    $sql = "SELECT id_user, username, role, status FROM users ORDER BY id_user ASC";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $result->free();
    }
    return $users;
}

$message = '';
if (isset($_GET['message'])) {
    $message = sanitize($_GET['message']);
}

$users = getAllUsers($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body class="admin-dashboard">

<div class="sidebar d-none d-md-block">
    <?php include 'layout/sidebar.php'; ?>
</div>

<div class="main-wrapper">
    <?php include 'layout/nav.php'; ?>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Daftar User</h3>
                            <a href="tambah_user.php" class="btn btn-primary">Tambah User</a>
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
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id_user']; ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                                <td><?php echo htmlspecialchars($user['status']); ?></td>
                                                <td>
                                                    <a href="edit_user.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <?php if ($user['username'] !== ($_SESSION['username'] ?? '')): ?>
                                                        <a href="delete_user.php?id=<?php echo $user['id_user']; ?>" class="btn btn-sm btn-outline-<?php echo $user['status'] === 'aktif' ? 'danger' : 'success'; ?>">
                                                            <?php echo $user['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                                        </a>
                                                    <?php endif; ?>
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
<script src="asset/js/main.js"></script>
</body>
</html>
