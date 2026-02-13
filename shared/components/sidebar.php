<?php
// Get current route for active menu
$currentRoute = $_GET['route'] ?? 'dashboard';
$user_level = $_SESSION['ses_level'] ?? 'User';
?>

<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="user-panel-content">
                <div class="user-panel-logo">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Crect fill='%23F3F4F6' width='48' height='48' rx='10'/%3E%3Cpath fill='%23D1D5DB' d='M24 10c5 0 9 4 9 9s-4 9-9 9-9-4-9-9 4-9 9-9zm0 21c7.5 0 15 3.75 15 7.5v4.5H9v-4.5c0-3.75 7.5-7.5 15-7.5z'/%3E%3C/svg%3E" alt="User" class="user-panel-logo-img">
                </div>
                <div class="user-panel-info">
                    <p class="user-panel-name"><?= htmlspecialchars($_SESSION['ses_nama'] ?? 'Guest') ?></p>
                    <span class="user-panel-role"><?= htmlspecialchars($_SESSION['ses_level'] ?? 'User') ?></span>
                    <a href="#" class="user-panel-status"><i class="fa fa-circle"></i> Online</a>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>

            <!-- Dashboard -->
            <li class="<?= $currentRoute === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= Router::url('dashboard') ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            <?php if ($user_level === 'Administrator'): ?>
            <!-- Master Data -->
            <li class="header">MASTER DATA</li>

            <li class="<?= strpos($currentRoute, 'paket') === 0 ? 'active' : '' ?>">
                <a href="<?= Router::url('paket') ?>">
                    <i class="fa fa-send"></i> <span>Data Paket</span>
                </a>
            </li>

            <li class="<?= strpos($currentRoute, 'pelanggan') === 0 ? 'active' : '' ?>">
                <a href="<?= Router::url('pelanggan') ?>">
                    <i class="fa fa-users"></i> <span>Data Pelanggan</span>
                </a>
            </li>

            <!-- Tagihan & Pembayaran -->
            <li class="header">TAGIHAN & PEMBAYARAN</li>

            <li class="<?= strpos($currentRoute, 'tagihan') === 0 ? 'active' : '' ?>">
                <a href="<?= Router::url('tagihan') ?>">
                    <i class="fa fa-file-text"></i> <span>Data Tagihan</span>
                </a>
            </li>

            <li class="<?= strpos($currentRoute, 'pembayaran-lunas') === 0 ? 'active' : '' ?>">
                <a href="<?= Router::url('pembayaran-lunas') ?>">
                    <i class="fa fa-check-circle"></i> <span>Pembayaran Lunas</span>
                </a>
            </li>

            <li class="<?= strpos($currentRoute, 'titipan') === 0 ? 'active' : '' ?>">
                <a href="<?= Router::url('titipan') ?>">
                    <i class="fa fa-money"></i> <span>Titipan</span>
                </a>
            </li>

            <!-- Lainnya -->
            <li class="header">LAINNYA</li>

            <li class="<?= strpos($currentRoute, 'log') === 0 ? 'active' : '' ?>">
                <a href="<?= Router::url('log-aktivitas') ?>">
                    <i class="fa fa-history"></i> <span>Log Aktivitas</span>
                </a>
            </li>

            <li>
                <a href="<?= Router::url('logout.php') ?>">
                    <i class="fa fa-sign-out"></i> <span>Logout</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </section>
</aside>
