<?php
// ==================== FILTER & DATA CALCULATION ====================
// ✅ SOURCE: Modified by Claude Code - Added total calculation & filter
// Get current month and year as default
$bulan_default = isset($_POST["bulan"]) ? $_POST["bulan"] : date('m');
$tahun_default = isset($_POST["tahun"]) ? $_POST["tahun"] : date('Y');

// Get filter values
$bulan = $bulan_default;
$tahun = $tahun_default;

// Get month name
$nama_bulan = "";
if (!empty($bulan)) {
	$sql_bulan = $koneksi->query("SELECT bulan FROM tb_bulan WHERE id_bulan='$bulan'");
	if ($data_bulan = $sql_bulan->fetch_assoc()) {
		$nama_bulan = $data_bulan['bulan'];
	}
}

// ✅ Calculate total tagihan for current month (Belum Bayar)
$sql_total_bl = $koneksi->query("SELECT COUNT(*) as jumlah, SUM(tagihan) as total
	FROM tb_tagihan
	WHERE bulan = '$bulan' AND tahun = '$tahun' AND status = 'BL'");
$data_total_bl = $sql_total_bl->fetch_assoc();
$jumlah_bl = $data_total_bl['jumlah'] ?? 0;
$total_bl = $data_total_bl['total'] ?? 0;

// ✅ Calculate total tagihan for current month (Lunas)
$sql_total_ls = $koneksi->query("SELECT COUNT(*) as jumlah, SUM(tagihan) as total
	FROM tb_tagihan
	WHERE bulan = '$bulan' AND tahun = '$tahun' AND status = 'LS'");
$data_total_ls = $sql_total_ls->fetch_assoc();
$jumlah_ls = $data_total_ls['jumlah'] ?? 0;
$total_ls = $data_total_ls['total'] ?? 0;
// ==================== END CALCULATION ====================
?>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">
						<i class="fa fa-table"></i> DATA TAGIHAN
					</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse">
							<i class="fa fa-minus"></i>
						</button>
					</div>
				</div>

				<!-- ==================== ✅ TOTAL SUMMARY (Modified by Claude Code - Made Bigger) ==================== -->
				<div class="box-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px;">
					<div class="row">
						<div class="col-md-6">
							<div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 10px; border-left: 6px solid #ff4757; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
								<h3 style="margin: 0; color: white; font-weight: bold;">
									<i class="fa fa-exclamation-circle"></i>
									Belum Bayar Bulan <?= $nama_bulan ?> <?= $tahun ?>
								</h3>
								<h2 style="margin: 15px 0 0 0; color: white; font-weight: bold;">
									<?= $jumlah_bl ?> Tagihan
									<span style="float: right; font-size: 28px; color: #ffd93d;">
										Rp<?= number_format($total_bl, 0, ',', '.') ?>
									</span>
								</h2>
							</div>
						</div>
						<div class="col-md-6">
							<div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 10px; border-left: 6px solid #2ed573; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
								<h3 style="margin: 0; color: white; font-weight: bold;">
									<i class="fa fa-check-circle"></i>
									Lunas Bulan <?= $nama_bulan ?> <?= $tahun ?>
								</h3>
								<h2 style="margin: 15px 0 0 0; color: white; font-weight: bold;">
									<?= $jumlah_ls ?> Tagihan
									<span style="float: right; font-size: 28px; color: #7bed9f;">
										Rp<?= number_format($total_ls, 0, ',', '.') ?>
									</span>
								</h2>
							</div>
						</div>
					</div>
				</div>
				<!-- ==================== END TOTAL SUMMARY ==================== -->

				<div class="box-body">
					<!-- ==================== ✅ FILTER FORM (Modified by Claude Code) ==================== -->
					<form class="form-inline" method="post" style="margin-bottom: 20px;">
						<div class="form-group" style="margin-right: 10px;">
							<label style="margin-right: 5px;">Bulan:</label>
							<select name="bulan" class="form-control" style="width: 180px;">
								<?php
								$query_bulan = "SELECT * FROM tb_bulan ORDER BY id_bulan";
								$hasil_bulan = mysqli_query($koneksi, $query_bulan);
								while ($row_bulan = mysqli_fetch_array($hasil_bulan)) {
									$selected = ($row_bulan['id_bulan'] == $bulan) ? 'selected' : '';
									echo "<option value='{$row_bulan['id_bulan']}' $selected>{$row_bulan['id_bulan']} - {$row_bulan['bulan']}</option>";
								}
								?>
							</select>
						</div>

						<div class="form-group" style="margin-right: 10px;">
							<label style="margin-right: 5px;">Tahun:</label>
							<select name="tahun" class="form-control" style="width: 120px;">
								<?php
								$current_year = date('Y');
								for ($y = 2022; $y <= $current_year + 10; $y++) {
									$selected = ($y == $tahun) ? 'selected' : '';
									echo "<option value='$y' $selected>$y</option>";
								}
								?>
							</select>
						</div>

						<button type="submit" name="filter" class="btn btn-primary">
							<i class="fa fa-filter"></i> Filter
						</button>

						<button type="button" class="btn btn-default" onclick="location.href='?page=data-tagihan'" style="margin-left: 5px;">
							<i class="fa fa-refresh"></i> Reset
						</button>
					</form>
					<!-- ==================== END FILTER FORM ==================== -->

					<!-- Info Alert -->
					<div class="alert alert-info alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<h4><i class="icon fa fa-info"></i> Data Tagihan</h4>
						Bulan : <strong><?php echo $nama_bulan; ?></strong> - Tahun : <strong><?php echo $tahun; ?></strong>
					</div>

					<!-- Data Table -->
					<div class="table-responsive">
						<table id="example1" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No</th>
									<th>ID PELANGGAN</th>
									<th>Nama</th>
									<th>Mac Modem</th>
									<th>Alamat</th>
									<th>Patokan</th>
									<th>Tagihan</th>
									<th>Status</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								$sql = $koneksi->query("SELECT
									p.id_pelanggan,
									p.nama,
									p.mac_modem,
									p.alamat,
									p.patokan,
									p.no_hp,
									t.id_tagihan,
									t.tagihan,
									t.kode_unik,
									t.status,
									t.tgl_bayar
									FROM tb_pelanggan p
									INNER JOIN tb_tagihan t ON p.id_pelanggan = t.id_pelanggan
									WHERE t.bulan = '$bulan'
									AND t.tahun = '$tahun'
									AND t.status = 'BL'
									ORDER BY p.nama ASC");

								while ($data = $sql->fetch_assoc()) {
								?>
									<tr>
										<td><?php echo $no++; ?></td>
										<td><?php echo $data['id_pelanggan']; ?></td>
										<td><?php echo $data['nama']; ?></td>
										<td><?php echo $data['mac_modem']; ?></td>
										<td><?php echo $data['alamat']; ?></td>
										<td><?php echo $data['patokan']; ?></td>
										<td><?php echo rupiah($data['tagihan']); ?></td>
										<td>
											<?php if ($data['status'] == 'BL') { ?>
												<span class="label label-danger">Belum Bayar</span>
											<?php } elseif ($data['status'] == 'LS') { ?>
												<span class="label label-success">Lunas</span>
												<br><small>(<?php echo $data['tgl_bayar']; ?>)</small>
											<?php } ?>
										</td>
										<td>
											<?php if ($data_user == 'Wiguna') { ?>
												<a href="?page=bayar-tagihan&kode=<?php echo $data['id_tagihan']; ?>"
												   title="Bayar Tagihan" class="btn btn-info btn-sm">
													<i class="glyphicon glyphicon-ok"></i> BAYAR
												</a>
											<?php } ?>
											<a href="kirim_manual.php?id_pelanggan=<?= $data['id_pelanggan'] ?>&id_tagihan=<?= $data['id_tagihan'] ?>"
											   target="_blank" title="Pesan WhatsApp" class="btn btn-success btn-sm">
												<i class="fa fa-whatsapp"></i> WA
											</a>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ==================== ✅ CUSTOM SEARCH SCRIPT (Added by Claude Code) ==================== -->
<script>
$(function() {
	// Initialize DataTable with custom search for ID, Nama, Mac Modem
	var table = $("#example1").DataTable({
		"lengthMenu": [[10, 25, 50, 100, 1000], [10, 25, 50, 100, 1000]],
		"pageLength": 25,
		"order": [[1, "asc"]], // Sort by ID Pelanggan by default
		"language": {
			"search": "Cari (ID/Nama/Mac):",
			"lengthMenu": "Tampilkan _MENU_ data per halaman",
			"info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
			"infoEmpty": "Tidak ada data",
			"infoFiltered": "(difilter dari _MAX_ total data)",
			"zeroRecords": "Data tidak ditemukan",
			"paginate": {
				"first": "Pertama",
				"last": "Terakhir",
				"next": "Selanjutnya",
				"previous": "Sebelumnya"
			}
		}
	});

	// Custom search function for ID Pelanggan, Nama, and Mac Modem
	$.fn.dataTable.ext.search.push(
		function(settings, data, dataIndex) {
			var searchTerm = $('.dataTables_filter input').val().toLowerCase();

			if (searchTerm === '') {
				return true; // Show all if search is empty
			}

			// Search in columns: ID Pelanggan (1), Nama (2), Mac Modem (3)
			var id_pelanggan = data[1].toLowerCase();
			var nama = data[2].toLowerCase();
			var mac_modem = data[3].toLowerCase();

			if (id_pelanggan.indexOf(searchTerm) !== -1 ||
				nama.indexOf(searchTerm) !== -1 ||
				mac_modem.indexOf(searchTerm) !== -1) {
				return true;
			}

			return false;
		}
	);

	// Redraw table on search input
	$('.dataTables_filter input').on('keyup', function() {
		table.draw();
	});
});
</script>
<!-- ==================== END CUSTOM SEARCH SCRIPT ==================== -->
