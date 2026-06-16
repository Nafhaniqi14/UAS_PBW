<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../users.php?message=' . urlencode('Akses tidak sah.'));
    exit;
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../users.php?message=' . urlencode('Akses tidak sah.'));
    exit;
}

include '../../koneksi_db.php';

$id_user = (int)($_POST['id'] ?? 0);
if ($id_user <= 0) {
    header('Location: ../users.php?message=' . urlencode('ID user tidak valid.'));
    exit;
}

// Jangan ubah status akun sendiri
if (isset($_SESSION['id_user']) && $_SESSION['id_user'] === $id_user) {
    header('Location: ../users.php?message=' . urlencode('Tidak dapat mengubah status akun sendiri.'));
    exit;
}

$stmt = $conn->prepare('SELECT status FROM users WHERE id_user = ?');
$stmt->bind_param('i', $id_user);
$stmt->execute();
$result = $stmt->get_result();
if ($user = $result->fetch_assoc()) {
    $newStatus = $user['status'] === 'aktif' ? 'nonaktif' : 'aktif';
    $stmt->close();
    $update = $conn->prepare('UPDATE users SET status = ? WHERE id_user = ?');
    $update->bind_param('si', $newStatus, $id_user);
    $update->execute();
    $update->close();
    header('Location: ../users.php?message=' . urlencode('Status user berhasil diubah.'));
    exit;
}
$stmt->close();
header('Location: ../users.php?message=' . urlencode('User tidak ditemukan.'));
exit;
