<?php
// Get current route for active menu
$currentRoute = $_GET['route'] ?? 'dashboard';
$user_level = $_SESSION['ses_level'] ?? 'User';
?>

<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= Router::url('dist/img/user2-160x160.jpg') ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?= htmlspecialchars($_SESSION['ses_nama'] ?? 'Guest') ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
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
