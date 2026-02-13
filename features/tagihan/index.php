<?php
// ==================== TAGIHAN CONTROLLER ====================
// Modern billing feature using existing tb_tagihan data
// No database changes - only UI improvements

// Load dependencies
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../shared/components/card.php';
require_once __DIR__ . '/../../shared/components/stats.php';

// Get database connection
$koneksi = DB::getConnection();

// Get current month and year
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Get stats
$stats = [];

// Total tagihan bulan ini
$query_total = "SELECT COUNT(*) as total FROM tb_tagihan WHERE bulan='$bulan' AND tahun='$tahun'";
$result_total = $koneksi->query($query_total);
$stats['total'] = $result_total->fetch_assoc()['total'];

// Total belum lunas
$query_belum = "SELECT COUNT(*) as total FROM tb_tagihan WHERE bulan='$bulan' AND tahun='$tahun' AND status='BL'";
$result_belum = $koneksi->query($query_belum);
$stats['belum_lunas'] = $result_belum->fetch_assoc()['total'];

// Total sudah lunas
$query_lunas = "SELECT COUNT(*) as total FROM tb_tagihan WHERE bulan='$bulan' AND tahun='$tahun' AND status='LS'";
$result_lunas = $koneksi->query($query_lunas);
$stats['lunas'] = $result_lunas->fetch_assoc()['total'];

// Total nominal
$query_nominal = "SELECT SUM(jumlah) as total FROM tb_tagihan WHERE bulan='$bulan' AND tahun='$tahun'";
$result_nominal = $koneksi->query($query_nominal);
$stats['nominal'] = $result_nominal->fetch_assoc()['total'] ?? 0;

// Get tagihan data
$query_tagihan = "SELECT t.*, p.nama, p.alamat, p.no_hp, pk.paket, pk.tarif
                  FROM tb_tagihan t
                  LEFT JOIN tb_pelanggan p ON t.id_pelanggan = p.id_pelanggan
                  LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket
                  WHERE t.bulan='$bulan' AND t.tahun='$tahun'
                  ORDER BY t.id_tagihan DESC";
$result_tagihan = $koneksi->query($query_tagihan);

// Prepare view data
$pageTitle = 'Data Tagihan';
$pageSubtitle = 'Kelola tagihan pelanggan bulanan';
$breadcrumbs = [
    ['label' => 'Tagihan']
];

// Include view
$viewFile = __DIR__ . '/views/list.php';

// Load layout
require_once __DIR__ . '/../../shared/layouts/main.php';
