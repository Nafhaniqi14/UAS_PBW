<?php
session_start();
if (!isset($_SESSION['L091n_t0K0']) || $_SESSION['L091n_t0K0'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php?message=' . urlencode('HARAP LOGIN TERLEBIH DAHULU!!!!'));
    exit;
}
include 'koneksi_db.php';

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = $_POST['role'] ?? 'petugas';
$status = $_POST['status'] ?? 'aktif';

if ($username === '' || $password === '') {
    header('Location: tambah_user.php?message=' . urlencode('Username dan password harus diisi.'));
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $username, $hashedPassword, $role, $status);
if ($stmt->execute()) {
    header('Location: users.php?message=' . urlencode('User berhasil ditambahkan.'));
    exit;
}
$stmt->close();
header('Location: tambah_user.php?message=' . urlencode('Gagal menambahkan user.'));
exit;


