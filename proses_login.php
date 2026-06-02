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

        $passwordMatched = password_verify($password, $user['password']);
        $legacyPassword = !$passwordMatched && $password === $user['password'];

        if ($passwordMatched || $legacyPassword) {
            if ($legacyPassword || password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare('UPDATE users SET password = ? WHERE id_user = ?');
                $updateStmt->bind_param('si', $newHash, $user['id_user']);
                $updateStmt->execute();
                $updateStmt->close();
            }

            session_regenerate_id(true);
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['L091n_t0K0'] = true;
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
