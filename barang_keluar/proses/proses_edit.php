<?php
session_start();
if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true) {
    header('Location: ../../index.php');
    exit;
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../data_barang_keluar.php?message=Akses tidak sah!');
    exit;
}

// Validasi CSRF token
if (!isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header('Location: ../data_barang_keluar.php?message=Akses tidak sah!');
    exit;
}

include '../../koneksi_db.php';

$id_keluar      = isset($_POST['id_keluar'])      ? intval($_POST['id_keluar'])      : 0;
$tanggal        = isset($_POST['tanggal'])         ? trim($_POST['tanggal'])          : '';
$id_barang      = isset($_POST['id_barang'])       ? intval($_POST['id_barang'])      : 0;
$jumlah         = isset($_POST['jumlah'])          ? intval($_POST['jumlah'])         : 0;
$tujuan         = isset($_POST['tujuan'])          ? trim($_POST['tujuan'])           : '';
$id_barang_lama = isset($_POST['id_barang_lama'])  ? intval($_POST['id_barang_lama']) : 0;
$jumlah_lama    = isset($_POST['jumlah_lama'])     ? intval($_POST['jumlah_lama'])    : 0;

// Batasi panjang string
$tanggal = substr($tanggal, 0, 10);
$tujuan  = substr($tujuan, 0, 100);

// Validasi input dasar
if ($id_keluar <= 0 || empty($tanggal) || $id_barang <= 0 || $jumlah <= 0) {
    header('Location: ../data_barang_keluar.php?message=Semua field harus diisi dengan benar');
    exit;
}

// Validasi format tanggal (harus Y-m-d)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    header('Location: ../data_barang_keluar.php?message=Semua field harus diisi dengan benar');
    exit;
}

$conn->begin_transaction();

// Validasi: pastikan id_keluar ada dan id_barang_lama sesuai DB
$sqlCekRecord = "SELECT id_barang, jumlah FROM barang_keluar WHERE id_keluar = ?";
if ($stmtCekRec = $conn->prepare($sqlCekRecord)) {
    $stmtCekRec->bind_param("i", $id_keluar);
    $stmtCekRec->execute();
    $resRec = $stmtCekRec->get_result();
    if ($rowRec = $resRec->fetch_assoc()) {
        // Ambil nilai asli dari DB, jangan percaya hidden input
        $id_barang_lama = intval($rowRec['id_barang']);
        $jumlah_lama    = intval($rowRec['jumlah']);
    } else {
        $conn->rollback();
        header('Location: ../data_barang_keluar.php?message=ID barang keluar tidak valid');
        exit;
    }
    $stmtCekRec->close();
} else {
    $conn->rollback();
    header('Location: ../data_barang_keluar.php?message=Gagal memperbarui barang keluar');
    exit;
}

// Kunci baris stok barang baru 
$stokSaat = 0;
$sqlCekStok = "SELECT stok FROM barang WHERE id_barang = ? FOR UPDATE";
if ($stmtCek = $conn->prepare($sqlCekStok)) {
    $stmtCek->bind_param("i", $id_barang);
    $stmtCek->execute();
    $result = $stmtCek->get_result();
    if ($row = $result->fetch_assoc()) {
        $stokSaat = $row['stok'];
    } else {
        $conn->rollback();
        header('Location: ../data_barang_keluar.php?message=Semua field harus diisi dengan benar');
        exit;
    }
    $stmtCek->close();
} else {
    $conn->rollback();
    header('Location: ../data_barang_keluar.php?message=Gagal memperbarui barang keluar');
    exit;
}

// Jika barang berbeda, kunci juga baris barang lama
if ($id_barang != $id_barang_lama) {
    $sqlKunciBrgLama = "SELECT stok FROM barang WHERE id_barang = ? FOR UPDATE";
    if ($stmtKunci = $conn->prepare($sqlKunciBrgLama)) {
        $stmtKunci->bind_param("i", $id_barang_lama);
        $stmtKunci->execute();
        $stmtKunci->close();
    }
}

// Hitung stok tersedia
// Jika barang sama: stok tersedia = stok saat ini + jumlah lama (dikembalikan dulu)
// Jika barang beda: stok tersedia = stok saat ini saja
$stokTersedia = ($id_barang == $id_barang_lama)
    ? ($stokSaat + $jumlah_lama)
    : $stokSaat;

if ($jumlah > $stokTersedia) {
    $conn->rollback();
    header('Location: ../data_barang_keluar.php?message=Stok barang tidak mencukupi');
    exit;
}

// Update data barang keluar
$sql = "UPDATE barang_keluar SET tanggal = ?, id_barang = ?, jumlah = ?, tujuan = ? WHERE id_keluar = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("siisi", $tanggal, $id_barang, $jumlah, $tujuan, $id_keluar);
    if ($stmt->execute()) {
        $stmt->close();

        // Kembalikan stok barang lama
        $sqlKembalikan = "UPDATE barang SET stok = stok + ? WHERE id_barang = ?";
        if ($stmtK = $conn->prepare($sqlKembalikan)) {
            $stmtK->bind_param("ii", $jumlah_lama, $id_barang_lama);
            $stmtK->execute();
            $stmtK->close();
        } else {
            $conn->rollback();
            header('Location: ../data_barang_keluar.php?message=Gagal memperbarui barang keluar');
            exit;
        }

        // Kurangi stok barang baru
        $sqlKurangi = "UPDATE barang SET stok = stok - ? WHERE id_barang = ?";
        if ($stmtT = $conn->prepare($sqlKurangi)) {
            $stmtT->bind_param("ii", $jumlah, $id_barang);
            if ($stmtT->execute()) {
                $stmtT->close();
                $conn->commit();
                header('Location: ../data_barang_keluar.php?message=Barang keluar berhasil diperbarui dan stok telah disesuaikan');
            } else {
                $stmtT->close();
                $conn->rollback();
                header('Location: ../data_barang_keluar.php?message=Gagal memperbarui barang keluar');
            }
        } else {
            $conn->rollback();
            header('Location: ../data_barang_keluar.php?message=Gagal memperbarui barang keluar');
        }
    } else {
        $stmt->close();
        $conn->rollback();
        header('Location: ../data_barang_keluar.php?message=Gagal memperbarui barang keluar');
    }
} else {
    $conn->rollback();
    header('Location: ../data_barang_keluar.php?message=Gagal memperbarui barang keluar');
}
?>