<?php
session_start();
if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: index.php');
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

$stmt = $conn->prepare('INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $username, $password, $role, $status);
if ($stmt->execute()) {
    header('Location: users.php?message=' . urlencode('User berhasil ditambahkan.'));
    exit;
}
$stmt->close();
header('Location: tambah_user.php?message=' . urlencode('Gagal menambahkan user.'));
exit;
