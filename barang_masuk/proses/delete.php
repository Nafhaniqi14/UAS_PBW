<?php
session_start();
if (!isset($_SESSION['login_Un51k4']) || $_SESSION['login_Un51k4'] !== true) {
    header('Location: ../../index.php');
    exit;
}

include '../../koneksi_db.php';

$id_masuk = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_masuk > 0) {
    // Ambil data jumlah dan id_barang dulu sebelum dihapus
    $jumlah_hapus    = 0;
    $id_barang_hapus = 0;
    $sqlAmbil = "SELECT id_barang, jumlah FROM barang_masuk WHERE id_masuk = ?";
    if ($stmtAmbil = $conn->prepare($sqlAmbil)) {
        $stmtAmbil->bind_param("i", $id_masuk);
        $stmtAmbil->execute();
        $result = $stmtAmbil->get_result();
        if ($row = $result->fetch_assoc()) {
            $id_barang_hapus = $row['id_barang'];
            $jumlah_hapus    = $row['jumlah'];
        }
        $stmtAmbil->close();
    }

    // Hapus data barang masuk
    $sql = "DELETE FROM barang_masuk WHERE id_masuk = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_masuk);
        if ($stmt->execute()) {
            // Kurangi stok barang kembali
            if ($id_barang_hapus > 0 && $jumlah_hapus > 0) {
                $sqlStok = "UPDATE barang SET stok = stok - ? WHERE id_barang = ?";
                if ($stmtStok = $conn->prepare($sqlStok)) {
                    $stmtStok->bind_param("ii", $jumlah_hapus, $id_barang_hapus);
                    $stmtStok->execute();
                    $stmtStok->close();
                }
            }
            header('Location: ../data_barang_masuk.php?message=Barang masuk berhasil dihapus dan stok telah disesuaikan');
        } else {
            header('Location: ../data_barang_masuk.php?message=Gagal menghapus barang masuk');
        }
        $stmt->close();
    }
} else {
    header('Location: ../data_barang_masuk.php?message=ID barang masuk tidak valid');
}
?>
