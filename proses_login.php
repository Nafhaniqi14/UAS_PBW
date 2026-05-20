<?php
session_start();
include 'koneksi_db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT id_user, username, password, role, status 
                            FROM users 
                            WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // cek status aktif
        if ($user['status'] !== 'aktif') {
            header("Location: index.php?message=" . urlencode("Akun nonaktif"));
            exit;
        }
        // cek password
        if ($password === $user['password']) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_Un51k4'] = true;
            // arahkan sesuai role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: karyawan_dashboard.php");
            }
            exit;
        } else {
            header("Location: index.php?message=" . urlencode("Password salah"));
            exit;
        }
    } else {
        header("Location: index.php?message=" . urlencode("Username tidak ditemukan"));
        exit;
    }
    $stmt->close();
}
?>