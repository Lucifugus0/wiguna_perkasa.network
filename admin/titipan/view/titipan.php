<?php
// ==================== ✅ TITIPAN VIEW (HANDLERS ARE IN INDEX.PHP) ====================
$is_teknisi = true;

// Generate delete token
if (!isset($_SESSION['delete_token'])) {
	$_SESSION['delete_token'] = bin2hex(random_bytes(16));
}

// Alert messages
$alert_message = '';
$alert_type = '';
if (isset($_SESSION['success_message'])) {
	$alert_message = $_SESSION['success_message'];
	$alert_type = 'success';
	unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
	$alert_message = $_SESSION['error_message'];
	$alert_type = 'error';
	unset($_SESSION['error_message']);
}
?>

<div class="card-header with-border">
	<h3 class="card-title">Data Titipan</h3>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="box box-info">
			<div class="box-header with-border" style="background: #f4f4f4; padding: 15px; border-radius: 3px;">
				<h3 class="box-title" style="margin-top: 0;"><i class="fa fa-plus-circle"></i> Form Tambah Titipan</h3>
			</div>
			<form method="POST" action="?page=titipan" id="formTitipan">
				<input type="hidden" name="simpan_titipan" value="1">
				<div class="box-body" style="padding: 20px;">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Cari Pelanggan <span class="text-danger">*</span></label>
								<input type="hidden" name="id_pelanggan" id="hidden_id_pelanggan" value="">
								<select class="form-control select2-titipan" id="id_pelanggan" required style="width: 100%;">
									<option value="">-- Pilih Pelanggan --</option>
									<?php
										$sql_plg = $koneksi->query("SELECT p.*, pk.paket, pk.tarif FROM tb_pelanggan p LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket ORDER BY p.nama ASC");
										while ($plg = $sql_plg->fetch_assoc()) {
									?>
									<option value="<?= $plg['id_pelanggan'] ?>"
										data-nama="<?= $plg['nama'] ?>"
										data-alamat="<?= $plg['alamat'] ?>"
										data-patokan="<?= $plg['patokan'] ?? '' ?>"
										data-paket="<?= $plg['paket'] ?? '' ?>"
										data-tarif="<?= $plg['tarif'] ?? 0 ?>"
										data-email="<?= $plg['email'] ?? '' ?>">
										<?= $plg['id_pelanggan'] ?> - <?= $plg['nama'] ?> - <?= $plg['email'] ?>
									</option>
									<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label>ID Pelanggan</label>
								<input type="text" class="form-control" id="txt_id_pelanggan" readonly>
							</div>
							<div class="form-group">
								<label>Nama Pelanggan</label>
								<input type="text" class="form-control" id="txt_nama" readonly>
							</div>
							<div class="form-group">
								<label>Alamat</label>
								<input type="text" class="form-control" id="txt_alamat" readonly>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Patokan</label>
								<input type="text" class="form-control" id="txt_patokan" readonly>
							</div>
							<div class="form-group">
								<label>Paket</label>
								<input type="text" class="form-control" id="txt_paket" readonly>
							</div>
							<div class="form-group">
								<label>Harga Paket</label>
								<input type="text" class="form-control" id="txt_tarif" readonly>
								<input type="hidden" id="harga_paket_value" value="0">
							</div>
							<div class="form-group">
								<label>Jumlah Titipan (Rp)</label>
								<input type="number" class="form-control" name="jumlah" id="jumlah_titipan" placeholder="Masukkan jumlah titipan (opsional)" min="0">
								<small class="text-muted">*Opsional - Kosongkan jika tidak ada titipan tambahan. Maksimal sesuai harga paket.</small>
							</div>
							<div class="form-group">
								<label>Keterangan</label>
								<textarea class="form-control" name="keterangan" rows="2" placeholder="Keterangan (opsional)"></textarea>
							</div>
						</div>
					</div>
					<hr>
					<button type="submit" class="btn btn-primary btn-lg">
						<i class="fa fa-save"></i> Simpan Titipan
					</button>
					<button type="reset" class="btn btn-default btn-lg" style="margin-left: 10px;">
						<i class="fa fa-refresh"></i> Reset Form
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<hr style="margin: 30px 0;">

<div class="row">
	<div class="col-md-12">
		<div class="box box-info">
			<div class="box-header with-border" style="background: #f4f4f4; padding: 15px; border-radius: 3px;">
				<h3 class="box-title" style="margin-top: 0;"><i class="fa fa-table"></i> Data Titipan (Belum Lunas)</h3>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="30">No</th>
								<th>ID Pelanggan</th>
								<th>Nama Pelanggan</th>
								<th>Alamat</th>
								<th>Patokan</th>
								<th>Paket</th>
								<th>Harga Paket</th>
								<th>Jumlah Titipan</th>
								<th>Keterangan</th>
								<th>Tanggal</th>
								<th width="100">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								$total_tarif = 0;
								$total_titipan = 0;
								$sql_titipan = $koneksi->query("SELECT t.*, p.nama, p.alamat, p.patokan, p.no_hp, p.no_hp_cadangan, pk.paket, pk.tarif
									FROM tb_titipan t
									LEFT JOIN tb_pelanggan p ON t.id_pelanggan = p.id_pelanggan
									LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket
									WHERE t.status = 'BL'
									ORDER BY t.id_titipan DESC");

								if ($sql_titipan) {
									while ($data = $sql_titipan->fetch_assoc()) {
										$total_tarif += $data['tarif'] ?? 0;
										$total_titipan += $data['jumlah'] ?? 0;
							?>
							<tr>
								<td><?= $no++ ?></td>
								<td><?= $data['id_pelanggan'] ?></td>
								<td><?= $data['nama'] ?></td>
								<td><?= $data['alamat'] ?></td>
								<td><?= $data['patokan'] ?? '-' ?></td>
								<td><?= $data['paket'] ?? '-' ?></td>
								<td>Rp <?= number_format($data['tarif'], 0, ',', '.') ?></td>
								<td><?= $data['jumlah'] ? 'Rp ' . number_format($data['jumlah'], 0, ',', '.') : '-' ?></td>
								<td><?= $data['keterangan'] ?? '-' ?></td>
								<td><?= $data['tgl_titipan'] ?></td>
								<td class="text-center">
									<button type="button" class="btn btn-primary" onclick="showQR(<?= $data['id_titipan'] ?>)" title="Lihat QR Code">
										<i class="fa fa-qrcode"></i>
									</button>
									<a href="?page=titipan&hapus=<?= $data['id_titipan'] ?>&token=<?= $_SESSION['delete_token'] ?>"
										onclick="return confirm('Yakin ingin menghapus titipan atas nama <?= addslashes($data['nama']) ?>?')"
										class="btn btn-danger"
										title="Hapus">
										<i class="fa fa-trash"></i>
									</a>
								</td>
							</tr>
							<?php
									}
								}
							?>
						</tbody>
						<tfoot>
							<tr style="background: #f9f9f9; font-weight: bold;">
								<td colspan="6" class="text-right">TOTAL:</td>
								<td>Rp <?= number_format($total_tarif, 0, ',', '.') ?></td>
								<td>Rp <?= number_format($total_titipan, 0, ',', '.') ?></td>
								<td colspan="3"></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="qrModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header" style="background: #605ca8; color: white;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"><i class="fa fa-qrcode"></i> QR Code Pembayaran Titipan</h4>
			</div>
			<div class="modal-body" id="qrModalBody">
				<div class="text-center" style="padding: 40px 20px;">
					<i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
					<p style="margin-top: 15px; font-size: 16px;">Generating QR Code...</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" id="btnDownloadQR" style="display:none;">
					<i class="fa fa-download"></i> Download QR Code (PNG)
				</button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
			</div>
		</div>
	</div>
</div>

<?php
// Save alert variables for script file (will be loaded after jQuery in index.php)
$GLOBALS['titipan_alert_message'] = $alert_message;
$GLOBALS['titipan_alert_type'] = $alert_type;
?>
