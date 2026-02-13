<?php
// ==================== WIGUNA PERKASA NETWORK ====================
// Main Entry Point with Modern Routing
// Created: 2026-02-13

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION["ses_username"]) || empty($_SESSION["ses_username"])) {
    header("location: member.php");
    exit;
}

// Load core files
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

// Initialize router
$router = new Router();

// ==================== ROUTE DEFINITIONS ====================

// Dashboard
$router->add('dashboard', function() {
    // For now, use existing admin dashboard
    $pageTitle = 'Dashboard';
    $content = '<h3>Welcome to Modern Dashboard</h3><p>Dashboard content will be here...</p>';
    require __DIR__ . '/shared/layouts/main.php';
});

// Tagihan (Billing) - MODERN VERSION
$router->add('tagihan', function() {
    require __DIR__ . '/features/tagihan/index.php';
});

// Pelanggan - Use existing (for backward compatibility)
$router->add('pelanggan', function() {
    $pageTitle = 'Data Pelanggan';
    $breadcrumbs = [['label' => 'Pelanggan']];
    $viewFile = __DIR__ . '/admin/pelanggan/data_pelanggan.php';
    ob_start();
    include $viewFile;
    $content = ob_get_clean();
    require __DIR__ . '/shared/layouts/main.php';
});

// Paket - Use existing (for backward compatibility)
$router->add('paket', function() {
    $pageTitle = 'Data Paket';
    $breadcrumbs = [['label' => 'Paket']];
    $viewFile = __DIR__ . '/admin/paket/data_paket.php';
    ob_start();
    include $viewFile;
    $content = ob_get_clean();
    require __DIR__ . '/shared/layouts/main.php';
});

// Titipan - Use existing refactored version
$router->add('titipan', function() {
    global $koneksi;

    // Load handlers first
    if (isset($_GET['ajax_qr'])) {
        require __DIR__ . '/admin/titipan/handler/ajax_qr.php';
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_titipan'])) {
        require __DIR__ . '/admin/titipan/handler/save.php';
        exit;
    }
    if (isset($_GET['hapus']) && isset($_GET['token'])) {
        require __DIR__ . '/admin/titipan/handler/delete.php';
        exit;
    }

    // Load view
    $pageTitle = 'Data Titipan';
    $breadcrumbs = [['label' => 'Titipan']];
    ob_start();
    include __DIR__ . '/admin/titipan/view/titipan.php';
    $content = ob_get_clean();

    // Load script
    $alert_message = $GLOBALS['titipan_alert_message'] ?? '';
    $alert_type = $GLOBALS['titipan_alert_type'] ?? '';
    ob_start();
    include __DIR__ . '/admin/titipan/view/titipan_script.php';
    $inlineScript = ob_get_clean();

    require __DIR__ . '/shared/layouts/main.php';
});

// Pembayaran Lunas
$router->add('pembayaran-lunas', function() {
    $pageTitle = 'Pembayaran Lunas';
    $breadcrumbs = [['label' => 'Pembayaran Lunas']];
    $viewFile = __DIR__ . '/admin/tagihan/lunas_tagihan.php';
    ob_start();
    include $viewFile;
    $content = ob_get_clean();
    require __DIR__ . '/shared/layouts/main.php';
});

// Log Aktivitas
$router->add('log-aktivitas', function() {
    $pageTitle = 'Log Aktivitas';
    $breadcrumbs = [['label' => 'Log Aktivitas']];
    $viewFile = __DIR__ . '/admin/log/log_pengguna.php';
    ob_start();
    include $viewFile;
    $content = ob_get_clean();
    require __DIR__ . '/shared/layouts/main.php';
});

// ==================== DISPATCH ROUTE ====================
$router->dispatch();
