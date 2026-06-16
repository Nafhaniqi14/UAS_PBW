<?php
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$currentPage = basename($_SERVER['PHP_SELF']);

// Deteksi level nesting untuk menentukan prefix path
$scriptPath = $_SERVER['SCRIPT_NAME'];
$depth = substr_count($scriptPath, '/') - 1;
$basePrefix = ($depth > 1) ? '../' : '';

function sidebarActive($currentPage, ...$pages) {
    return in_array($currentPage, $pages, true) ? 'active' : '';
}
$dashboardUrl = $role === 'admin' ? $basePrefix . 'admin_dashboard.php' : $basePrefix . 'karyawan_dashboard.php';
?>
<div class="sidebar">
    <h4 class="text-white text-center mb-4">
        MATERIAL POINT
    </h4>

    <a class="<?php echo sidebarActive($currentPage, 'admin_dashboard.php', 'karyawan_dashboard.php'); ?>" href="<?php echo htmlspecialchars($dashboardUrl); ?>">
        <img class="menu-icon" src="<?php echo $basePrefix; ?>asset/img/Dashboard-icon.svg" alt="Dashboard Icon">
        Dashboard
    </a>
    <a class="<?php echo sidebarActive($currentPage, 'data_barang.php'); ?>" href="<?php echo $basePrefix; ?>barang/data_barang.php">
        <img class="menu-icon" src="<?php echo $basePrefix; ?>asset/img/Data-Barang-icon.svg" alt="Data Barang Icon">
        Data Barang
    </a>
    <?php if ($role === 'admin') : ?>
        <a class="<?php echo sidebarActive($currentPage, 'data_kategori.php'); ?>" href="<?php echo $basePrefix; ?>kategori/data_kategori.php">
            <img class="menu-icon" src="<?php echo $basePrefix; ?>asset/img/Kategori-icon.svg" alt="Kategori Icon">
            Kategori
        </a>
        <a class="<?php echo sidebarActive($currentPage, 'data_supplier.php', 'edit_supplier.php', 'tambah_supplier.php'); ?>" href="<?php echo $basePrefix; ?>supplier/data_supplier.php">
            <img class="menu-icon" src="<?php echo $basePrefix; ?>asset/img/Supplier-icon.svg" alt="Supplier Icon">
            Supplier
        </a>
    <?php endif; ?>
    <a class="<?php echo sidebarActive($currentPage, 'data_barang_masuk.php'); ?>" href="<?php echo $basePrefix; ?>barang_masuk/data_barang_masuk.php">
        <img class="menu-icon" src="<?php echo $basePrefix; ?>asset/img/Barang-masuk-icon.svg" alt="Barang Masuk Icon">
        Barang Masuk
    </a>
    <a class="<?php echo sidebarActive($currentPage, 'data_barang_keluar.php'); ?>" href="<?php echo $basePrefix; ?>barang_keluar/data_barang_keluar.php">
        <img class="menu-icon" src="<?php echo $basePrefix; ?>asset/img/Barang-keluar-icon.svg" alt="Barang Keluar Icon">
        Barang Keluar
    </a>
    <?php if ($role === 'admin') : ?>
        <a class="<?php echo sidebarActive($currentPage, 'users.php', 'edit_user.php', 'tambah_user.php'); ?>" href="<?php echo $basePrefix; ?>user/users.php">
            <img class="menu-icon" src="<?php echo $basePrefix; ?>asset/img/user-icon.svg" alt="User Icon">
            User
        </a>
    <?php endif; ?>
</div>