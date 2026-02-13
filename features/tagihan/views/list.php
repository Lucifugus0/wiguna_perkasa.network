<?php
// ==================== TAGIHAN LIST VIEW ====================
// Modern billing list with stats and filters
?>

<!-- Stats Row with Header -->
<div class="row stats-with-header">
    <div class="col-md-12 stats-header-col">
        <h2 class="stats-page-title">Data Tagihan</h2>
    </div>
    <div class="col-lg-3 col-xs-6">
        <?= StatsComponent::render([
            'title' => number_format($stats['total']),
            'subtitle' => 'Total Tagihan',
            'icon' => 'fa-file-text',
            'color' => 'aqua',
            'link' => 'tagihan',
            'linkText' => 'Lihat Detail'
        ]) ?>
    </div>

    <div class="col-lg-3 col-xs-6">
        <?= StatsComponent::render([
            'title' => number_format($stats['belum_lunas']),
            'subtitle' => 'Belum Lunas',
            'icon' => 'fa-clock-o',
            'color' => 'yellow',
            'link' => 'tagihan?status=BL',
            'linkText' => 'Lihat Detail'
        ]) ?>
    </div>

    <div class="col-lg-3 col-xs-6">
        <?= StatsComponent::render([
            'title' => number_format($stats['lunas']),
            'subtitle' => 'Sudah Lunas',
            'icon' => 'fa-check-circle',
            'color' => 'green',
            'link' => 'pembayaran-lunas',
            'linkText' => 'Lihat Detail'
        ]) ?>
    </div>

    <div class="col-lg-3 col-xs-6">
        <?= StatsComponent::render([
            'title' => 'Rp ' . number_format($stats['nominal'], 0, ',', '.'),
            'subtitle' => 'Total Nominal',
            'icon' => 'fa-money',
            'color' => 'red',
            'link' => '#',
            'linkText' => 'Detail'
        ]) ?>
    </div>
</div>

<!-- Filter Bar -->
<div class="row">
    <div class="col-md-12">
        <div class="filter-bar">
            <form method="GET" action="<?= Router::url('tagihan') ?>" class="form-inline">
                <div class="form-group">
                    <label>Bulan:</label>
                    <select name="bulan" class="form-control">
                        <?php
                        $months = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                                   '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                                   '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
                        foreach ($months as $key => $month):
                        ?>
                            <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>><?= $month ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tahun:</label>
                    <select name="tahun" class="form-control">
                        <?php for ($y = 2024; $y <= 2030; $y++): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-filter"></i> Filter
                </button>

                <a href="<?= Router::url('tagihan/buat') ?>" class="btn btn-success pull-right">
                    <i class="fa fa-plus"></i> Buat Tagihan
                </a>
            </form>
        </div>
    </div>
</div>

<!-- Tagihan Table -->
<div class="row">
    <div class="col-md-12">
        <?= Component::card([
            'title' => 'Daftar Tagihan',
            'subtitle' => 'Periode: ' . $months[$bulan] . ' ' . $tahun,
            'icon' => 'fa-table',
            'type' => 'primary',
            'content' => renderTagihanTable($result_tagihan)
        ]) ?>
    </div>
</div>

<?php
/**
 * Render tagihan table
 */
function renderTagihanTable($result) {
    ob_start();
    ?>
    <div class="table-responsive">
        <table class="table table-modern table-bordered table-striped datatable">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>ID Pelanggan</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Paket</th>
                    <th>Tagihan</th>
                    <th>Status</th>
                    <th>Tgl Bayar</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = $result->fetch_assoc()):
                    $status_badge = $row['status'] == 'LS' ? 'success' : 'warning';
                    $status_text = $row['status'] == 'LS' ? 'Lunas' : 'Belum Lunas';
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= $row['id_pelanggan'] ?></strong></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                    <td><?= htmlspecialchars($row['paket'] ?? '-') ?></td>
                    <td>Rp <?= number_format($row['tagihan'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge badge-<?= $status_badge ?>">
                            <?= $status_text ?>
                        </span>
                    </td>
                    <td><?= $row['tgl_bayar'] ?? '-' ?></td>
                    <td>
                        <?php if ($row['status'] == 'BL'): ?>
                            <a href="<?= Router::url('tagihan/bayar/' . $row['id_tagihan']) ?>"
                               class="btn btn-success btn-xs btn-action"
                               title="Bayar">
                                <i class="fa fa-money"></i>
                            </a>
                        <?php endif; ?>

                        <a href="<?= Router::url('tagihan/detail/' . $row['id_tagihan']) ?>"
                           class="btn btn-info btn-xs btn-action"
                           title="Detail">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="<?= Router::url('tagihan/hapus/' . $row['id_tagihan']) ?>"
                           class="btn btn-danger btn-xs btn-action"
                           data-confirm-delete="Hapus tagihan atas nama <?= htmlspecialchars($row['nama']) ?>?"
                           title="Hapus">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
?>
