<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}
include 'koneksi_db.php';

$id_user = (int) ($_POST['id_user'] ?? 0);
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = $_POST['role'] ?? 'petugas';
$status = $_POST['status'] ?? 'aktif';

if ($id_user <= 0 || $username === '') {
    header('Location: users.php?message=' . urlencode('Data tidak valid.'));
    exit;
}

if ($password !== '') {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE users SET username = ?, password = ?, role = ?, status = ? WHERE id_user = ?');
    $stmt->bind_param('ssssi', $username, $hashedPassword, $role, $status, $id_user);
} else {
    $stmt = $conn->prepare('UPDATE users SET username = ?, role = ?, status = ? WHERE id_user = ?');
    $stmt->bind_param('sssi', $username, $role, $status, $id_user);
}

if ($stmt->execute()) {
    header('Location: users.php?message=' . urlencode('User berhasil diperbarui.'));
    exit;
}
$stmt->close();
header('Location: users.php?message=' . urlencode('Gagal memperbarui user.'));
exit;


