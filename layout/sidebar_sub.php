<?php
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$currentPage = basename($_SERVER['PHP_SELF']);
function sidebarActive($currentPage, ...$pages) {
    return in_array($currentPage, $pages, true) ? 'active' : '';
}
// role 'admin' -> admin_dashboard, role 'petugas' -> karyawan_dashboard
$dashboardUrl = $role === 'admin' ? '../admin_dashboard.php' : '../karyawan_dashboard.php';
?>
<div class="sidebar">
    <h4 class="text-white text-center mb-4">
        MATERIAL POINT
    </h4>

    <a class="<?php echo sidebarActive($currentPage, 'admin_dashboard.php', 'karyawan_dashboard.php'); ?>" href="<?php echo htmlspecialchars($dashboardUrl); ?>">
        <img class="menu-icon" src="../asset/img/Dashboard-icon.svg" alt="Dashboard Icon">
        Dashboard
    </a>
    <a class="<?php echo sidebarActive($currentPage, 'data_barang.php'); ?>" href="../barang/data_barang.php">
        <img class="menu-icon" src="../asset/img/Data-Barang-icon.svg" alt="Data Barang Icon">
        Data Barang
    </a>
    <?php if ($role === 'admin') : ?>
        <a class="<?php echo sidebarActive($currentPage, 'data_kategori.php'); ?>" href="../kategori/data_kategori.php">
            <img class="menu-icon" src="../asset/img/Kategori-icon.svg" alt="Kategori Icon">
            Kategori
        </a>
        <a class="<?php echo sidebarActive($currentPage, 'data_supplier.php', 'edit_supplier.php', 'tambah_supplier.php'); ?>" href="../data_supplier.php">
            <img class="menu-icon" src="../asset/img/Supplier-icon.svg" alt="Supplier Icon">
            Supplier
        </a>
    <?php endif; ?>
    <a class="<?php echo sidebarActive($currentPage, 'data_barang_masuk.php', 'tambah_barang_masuk.php', 'edit_barang_masuk.php'); ?>" href="../barang_masuk/data_barang_masuk.php">
        <img class="menu-icon" src="../asset/img/Barang-masuk-icon.svg" alt="Barang Masuk Icon">
        Barang Masuk
    </a>
    <a class="<?php echo sidebarActive($currentPage, 'data_barang_keluar.php', 'tambah_barang_keluar.php', 'edit_barang_keluar.php'); ?>" href="../barang_keluar/data_barang_keluar.php">
        <img class="menu-icon" src="../asset/img/Barang-keluar-icon.svg" alt="Barang Keluar Icon">
        Barang Keluar
    </a>
    <?php if ($role === 'admin') : ?>
        <a class="<?php echo sidebarActive($currentPage, 'users.php', 'edit_user.php', 'tambah_user.php'); ?>" href="../users.php">
            <img class="menu-icon" src="../asset/img/user-icon.svg" alt="User Icon">
            User
        </a>
    <?php endif; ?>
</div>
