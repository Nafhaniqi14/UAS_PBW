<?php
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$dashboardUrl = $role === 'admin' ? '../admin_dashboard.php' : '../karyawan_dashboard.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-sidebar shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-outline-light btn-sm me-3 sidebar-toggle d-flex align-items-center" id="sidebarToggle" type="button" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
    
        <a class="navbar-brand fw-bold me-auto" href="<?php echo htmlspecialchars($dashboardUrl); ?>">
            <span id="navbarBrandText">MATERIAL  POINT</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
