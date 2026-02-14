<?php
	ob_start(); // Mulai output buffering
	session_start();
	require_once '../inc/koneksi.php';
	require_once '../inc/rupiah.php';

	$password_teknisi = 'berhasil';

	// Handle AJAX QR request first (before any output)
	if (isset($_GET['ajax_qr'])) {
		header('Content-Type: application/json');
		$id_titipan = intval($_GET['ajax_qr']);

		$qr_data = $koneksi->query("SELECT t.*, p.nama, p.alamat, p.patokan, pk.paket, pk.tarif
			FROM tb_titipan t
			LEFT JOIN tb_pelanggan p ON t.id_pelanggan = p.id_pelanggan
			LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket
			WHERE t.id_titipan = $id_titipan AND t.status = 'BL'")->fetch_assoc();

		if ($qr_data) {
			$harga_paket = $qr_data['tarif'] ?? 0;
			$jumlah_titipan = $qr_data['jumlah'] ?? 0;
			$total_bayar = $harga_paket + $qr_data['kode_unik'];

			$ch_qris = curl_init();
			curl_setopt_array($ch_qris, [
				CURLOPT_URL => "https://qrisku.my.id/api",
				CURLOPT_POST => TRUE,
				CURLOPT_POSTFIELDS => json_encode([
					'amount' => $total_bayar,
					'qris_statis' => '00020101021126690021ID.CO.BANKMANDIRI.WWW01189360000801821981980211718219819800303UMI51440014ID.CO.QRIS.WWW0215ID10254094363220303UMI5204274153033605802ID5922Wiguna Perkasa Network6013Cirebon (Kab)61054516262070703A016304FA5D'
				]),
				CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_FOLLOWLOCATION => TRUE
			]);
			$ch_qris_response = json_decode(curl_exec($ch_qris));
			curl_close($ch_qris);

			if ($ch_qris_response && isset($ch_qris_response->qris_base64)) {
				echo json_encode([
					'success' => true,
					'qr_base64' => $ch_qris_response->qris_base64,
					'id_pelanggan' => $qr_data['id_pelanggan'],
					'nama' => $qr_data['nama'],
					'alamat' => $qr_data['alamat'],
					'patokan' => $qr_data['patokan'] ?? '-',
					'paket' => $qr_data['paket'] ?? '-',
					'harga_paket' => number_format($harga_paket, 0, ',', '.'),
					'jumlah_titipan' => $jumlah_titipan ? number_format($jumlah_titipan, 0, ',', '.') : '-',
					'kode_unik' => number_format($qr_data['kode_unik'], 0, ',', '.'),
					'total_bayar' => number_format($total_bayar, 0, ',', '.'),
					'keterangan' => $qr_data['keterangan'] ?? '-',
					'tanggal' => $qr_data['tgl_titipan']
				]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal generate QR Code']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'Data titipan tidak ditemukan']);
		}
		exit;
	}

	// Prevent caching for normal page requests
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Pragma: no-cache");

	$is_teknisi = isset($_SESSION['is_teknisi_titipan']) ? true : false;
	if (isset($_GET['password']) && $_GET['password'] == $password_teknisi) {
		$_SESSION['is_teknisi_titipan'] = true;
		header("Location: index.php");
		exit;
	}

	// Proses Simpan Titipan
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_titipan'])) {
		if (isset($_POST['password']) && $_POST['password'] == $password_teknisi) {
			$id_pelanggan = $koneksi->real_escape_string($_POST['id_pelanggan']);
			$jumlah = !empty($_POST['jumlah']) ? intval($_POST['jumlah']) : 0;
			$keterangan = $koneksi->real_escape_string($_POST['keterangan'] ?? '');
			$kode_unik = intval(preg_replace('/[^0-9]/', '', $id_pelanggan));
			$bulan = date('m');
			$tahun = date('Y');
			$tgl_titipan = date('Y-m-d');

			$result = $koneksi->query("INSERT INTO tb_titipan (id_pelanggan, bulan, tahun, jumlah, kode_unik, status, tgl_titipan, keterangan)
				VALUES ('$id_pelanggan', '$bulan', '$tahun', $jumlah, $kode_unik, 'BL', '$tgl_titipan', '$keterangan')");

			if ($result) {
				$_SESSION['success_message'] = 'Data Titipan Berhasil Disimpan';
			} else {
				$_SESSION['error_message'] = 'Gagal menyimpan: ' . $koneksi->error;
			}
			header("Location: index.php", true, 303);
			exit;
		} else {
			$_SESSION['error_message'] = 'Password Teknisi Salah';
			header("Location: index.php", true, 303);
			exit;
		}
	}

	// Proses Hapus Titipan
	if (isset($_GET['hapus']) && isset($_GET['token'])) {
		// Validasi token untuk mencegah double submission
		if (isset($_SESSION['delete_token']) && $_GET['token'] === $_SESSION['delete_token']) {
			$id_titipan = intval($_GET['hapus']);
			$koneksi->query("DELETE FROM tb_titipan WHERE id_titipan = $id_titipan");
			$_SESSION['success_message'] = 'Data Titipan Berhasil Dihapus';
			unset($_SESSION['delete_token']); // Hapus token setelah digunakan
		}
		header("Location: index.php", true, 303);
		exit;
	}

	// Generate token untuk hapus
	if (!isset($_SESSION['delete_token'])) {
		$_SESSION['delete_token'] = bin2hex(random_bytes(16));
	}

	// Ambil pesan dari session
	$alert = '';
	if (isset($_SESSION['success_message'])) {
		$alert = "Swal.fire({title: '" . $_SESSION['success_message'] . "', text: '', icon: 'success', confirmButtonText: 'OKE'});";
		unset($_SESSION['success_message']);
	}
	if (isset($_SESSION['error_message'])) {
		$alert = "Swal.fire({title: '" . $_SESSION['error_message'] . "', text: '', icon: 'error', confirmButtonText: 'OKE'});";
		unset($_SESSION['error_message']);
	}

	// Generate QR Code (AJAX)
	if (isset($_GET['ajax_qr'])) {
		header('Content-Type: application/json');
		$id_titipan = intval($_GET['ajax_qr']);

		$qr_data = $koneksi->query("SELECT t.*, p.nama, p.alamat, pk.tarif
			FROM tb_titipan t
			LEFT JOIN tb_pelanggan p ON t.id_pelanggan = p.id_pelanggan
			LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket
			WHERE t.id_titipan = $id_titipan AND t.status = 'BL'")->fetch_assoc();

		if ($qr_data) {
			$total_bayar = ($qr_data['tarif'] ?? 0) + $qr_data['kode_unik'];

			$ch_qris = curl_init();
			curl_setopt_array($ch_qris, [
				CURLOPT_URL => "https://qrisku.my.id/api",
				CURLOPT_POST => TRUE,
				CURLOPT_POSTFIELDS => json_encode([
					'amount' => $total_bayar,
					'qris_statis' => '00020101021126690021ID.CO.BANKMANDIRI.WWW01189360000801821981980211718219819800303UMI51440014ID.CO.QRIS.WWW0215ID10254094363220303UMI5204274153033605802ID5922Wiguna Perkasa Network6013Cirebon (Kab)61054516262070703A016304FA5D'
				]),
				CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_FOLLOWLOCATION => TRUE
			]);
			$ch_qris_response = json_decode(curl_exec($ch_qris));
			curl_close($ch_qris);

			if ($ch_qris_response && isset($ch_qris_response->qris_base64)) {
				echo json_encode([
					'success' => true,
					'qr_base64' => $ch_qris_response->qris_base64,
					'id_pelanggan' => $qr_data['id_pelanggan'],
					'nama' => $qr_data['nama'],
					'alamat' => $qr_data['alamat'],
					'tarif' => number_format($qr_data['tarif'], 0, ',', '.'),
					'kode_unik' => number_format($qr_data['kode_unik'], 0, ',', '.'),
					'total_bayar' => number_format($total_bayar, 0, ',', '.'),
					'keterangan' => $qr_data['keterangan'] ?? '-',
					'tanggal' => $qr_data['tgl_titipan']
				]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Gagal generate QR Code']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'Data titipan tidak ditemukan']);
		}
		exit;
	}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>TITIPAN BULANAN - WIGUNA PERKASA NETWORK</title>
	<link rel="icon" href="../dist/img/komp.png">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="../plugins/select2/select2.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
	<link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</head>

<body class="hold-transition skin-purple">
	<div class="row" style="margin: 20px;">
		<div class="col-md-12 col-lg-10">
			<div class="card">
				<div class="card-header" style="padding: 15px; background: #605ca8; color: white; border-radius: 3px 3px 0 0;">
					<h4 class="mb-0" style="margin: 0;"><b>TITIPAN BULANAN</b> - Wiguna Perkasa Network</h4>
				</div>
				<div class="card-body" style="padding: 20px;">
					<?php if ($is_teknisi == false) { ?>
					<!-- Form Login Teknisi -->
					<form method="GET" class="row">
						<div class="col-md-6 form-group">
							<label for="password">Password Teknisi</label>
							<input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password teknisi" required>
						</div>
						<div class="col-md-12">
							<button type="submit" class="btn btn-primary"><i class="fa fa-lock"></i> Masuk Sebagai Teknisi</button>
							<button type="reset" class="btn btn-default" style="margin-left: 10px;"><i class="fa fa-refresh"></i> Reset</button>
						</div>
					</form>
					<?php } else { ?>

					<!-- Form Tambah Titipan -->
					<div style="background: #f4f4f4; padding: 15px; border-radius: 3px; margin-bottom: 20px;">
						<h4 style="margin-top: 0;"><i class="fa fa-plus-circle"></i> Form Tambah Titipan</h4>
					</div>
					<form method="POST" id="formTitipan">
						<input type="hidden" name="simpan_titipan" value="1">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Cari Pelanggan <span class="text-danger">*</span></label>
									<select class="form-control select2" name="id_pelanggan" id="id_pelanggan" required style="width: 100%;">
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
								<div class="form-group">
									<label for="password_teknisi">Password Teknisi <span class="text-danger">*</span></label>
									<input type="password" name="password" id="password_teknisi" class="form-control" placeholder="Masukkan password teknisi" required>
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
					</form>

					<hr style="margin: 30px 0;">

					<!-- Tabel Data Titipan -->
					<div style="background: #f4f4f4; padding: 15px; border-radius: 3px; margin-bottom: 20px;">
						<h4 style="margin-top: 0;"><i class="fa fa-table"></i> Data Titipan (Belum Lunas)</h4>
					</div>
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
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal QR Code -->
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
					<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery 2.2.3 -->
	<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
	<!-- Bootstrap 3.3.6 -->
	<script src="../bootstrap/js/bootstrap.min.js"></script>
	<!-- Select2 -->
	<script src="../plugins/select2/select2.full.min.js"></script>
	<!-- DataTables -->
	<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js"></script>

	<script>
		// Tampilkan alert dari session
		<?php if (!empty($alert)) { ?>
		<?= $alert ?>
		// Auto-reset form setelah success message
		$(document).ready(function() {
			$('#formTitipan')[0].reset();
			$('#id_pelanggan').val('').trigger('change');
			$('#txt_id_pelanggan, #txt_nama, #txt_alamat, #txt_patokan, #txt_paket, #txt_tarif').val('');
			$('#harga_paket_value').val(0);
			$('#jumlah_titipan').removeAttr('max').val('');
			$('#password_teknisi').val('');
		});
		<?php } ?>

		$(function() {
			// Initialize DataTables
			$("#example1").DataTable({
				"paging": true,
				"lengthChange": true,
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
				"searching": true,
				"ordering": true,
				"info": true,
				"autoWidth": false,
				"language": {
					"lengthMenu": "Tampilkan _MENU_ data per halaman",
					"zeroRecords": "Data tidak ditemukan",
					"info": "Menampilkan halaman _PAGE_ dari _PAGES_",
					"infoEmpty": "Tidak ada data tersedia",
					"infoFiltered": "(difilter dari _MAX_ total data)",
					"search": "Cari:",
					"paginate": {
						"first": "Pertama",
						"last": "Terakhir",
						"next": "Selanjutnya",
						"previous": "Sebelumnya"
					}
				}
			});

			// Initialize Select2 dengan custom matcher untuk search ID, nama, dan email
			$(".select2").select2({
				placeholder: "-- Pilih Pelanggan (cari berdasarkan ID, Nama, atau Email) --",
				allowClear: true,
				matcher: function(params, data) {
					// Jika tidak ada search term, tampilkan semua
					if ($.trim(params.term) === '') {
						return data;
					}

					// Jika tidak ada data atau children
					if (typeof data.text === 'undefined') {
						return null;
					}

					// Ubah search term ke lowercase untuk case-insensitive search
					var term = params.term.toLowerCase();
					var text = data.text.toLowerCase();

					// Get email dari data attribute
					var $element = $(data.element);
					var email = ($element.data('email') || '').toString().toLowerCase();
					var id = ($element.val() || '').toString().toLowerCase();
					var nama = ($element.data('nama') || '').toString().toLowerCase();

					// Search di ID pelanggan, nama, email, atau text option
					if (text.indexOf(term) > -1 ||
						id.indexOf(term) > -1 ||
						nama.indexOf(term) > -1 ||
						email.indexOf(term) > -1) {
						return data;
					}

					return null;
				}
			});

			// Auto-fill data pelanggan saat dipilih
			$('#id_pelanggan').on('change', function() {
				var selected = $(this).find(':selected');
				if (selected.val()) {
					// Auto-fill data pelanggan (readonly fields)
					$('#txt_id_pelanggan').val(selected.val());
					$('#txt_nama').val(selected.data('nama'));
					$('#txt_alamat').val(selected.data('alamat'));
					$('#txt_patokan').val(selected.data('patokan') || '-');
					$('#txt_paket').val(selected.data('paket') || '-');

					var tarif = selected.data('tarif') || 0;
					$('#txt_tarif').val(tarif ? 'Rp ' + Number(tarif).toLocaleString('id-ID') : '-');
					$('#harga_paket_value').val(tarif);
					$('#jumlah_titipan').attr('max', tarif);

					// Reset input fields (Jumlah Titipan & Keterangan tetap kosong)
					$('#jumlah_titipan').val('');
					$('textarea[name="keterangan"]').val('');
				} else {
					// Reset semua field jika tidak ada pelanggan dipilih
					$('#txt_id_pelanggan, #txt_nama, #txt_alamat, #txt_patokan, #txt_paket, #txt_tarif').val('');
					$('#harga_paket_value').val(0);
					$('#jumlah_titipan').removeAttr('max').val('');
					$('textarea[name="keterangan"]').val('');
				}
			});

			// Validasi jumlah titipan tidak melebihi harga paket
			$('#jumlah_titipan').on('input change', function() {
				var jumlah = parseInt($(this).val()) || 0;
				var maxHarga = parseInt($('#harga_paket_value').val()) || 0;

				if (jumlah > maxHarga && maxHarga > 0) {
					Swal.fire({
						title: 'Jumlah Titipan Melebihi Harga Paket!',
						text: 'Jumlah titipan tidak boleh lebih dari Rp ' + Number(maxHarga).toLocaleString('id-ID'),
						icon: 'warning',
						confirmButtonText: 'OK'
					});
					$(this).val(maxHarga);
				}
			});

			// Validasi sebelum submit form & prevent double submit
			var formSubmitting = false;
			$('#formTitipan').on('submit', function(e) {
				// Prevent double submit
				if (formSubmitting) {
					e.preventDefault();
					return false;
				}

				var jumlah = parseInt($('#jumlah_titipan').val()) || 0;
				var maxHarga = parseInt($('#harga_paket_value').val()) || 0;

				if (jumlah > maxHarga && maxHarga > 0) {
					e.preventDefault();
					Swal.fire({
						title: 'Jumlah Titipan Melebihi Harga Paket!',
						text: 'Jumlah titipan tidak boleh lebih dari Rp ' + Number(maxHarga).toLocaleString('id-ID'),
						icon: 'error',
						confirmButtonText: 'OK'
					});
					return false;
				}

				// Mark form as submitting
				formSubmitting = true;
				$(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
			});
		});

		// Function untuk show QR Code via AJAX
		function showQR(id_titipan) {
			$('#qrModal').modal('show');
			$('#qrModalBody').html(`
				<div class="text-center" style="padding: 40px 20px;">
					<i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
					<p style="margin-top: 15px; font-size: 16px;">Generating QR Code...</p>
				</div>
			`);

			$.ajax({
				url: '?ajax_qr=' + id_titipan,
				method: 'GET',
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						$('#qrModalBody').html(`
							<div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
								<!-- Header Struk -->
								<div style="background: linear-gradient(135deg, #605ca8 0%, #4a4a8a 100%); padding: 25px; text-align: center; color: white;">
									<h3 style="margin: 0 0 5px 0; font-size: 24px; font-weight: bold;">
										<i class="fa fa-receipt"></i> STRUK TITIPAN
									</h3>
									<p style="margin: 0; font-size: 14px; opacity: 0.9;">Wiguna Perkasa Network</p>
									<p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.8;">Tanggal: ${response.tanggal}</p>
								</div>

								<div style="padding: 25px;">
									<!-- Data Pelanggan -->
									<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #605ca8;">
										<h4 style="margin: 0 0 12px 0; color: #605ca8; font-size: 16px; font-weight: bold;">
											<i class="fa fa-user"></i> Data Pelanggan
										</h4>
										<table style="width: 100%; font-size: 14px;">
											<tr>
												<td style="padding: 5px 10px 5px 0; width: 140px; font-weight: 600; color: #555;">ID Pelanggan</td>
												<td style="padding: 5px 0;">: ${response.id_pelanggan}</td>
											</tr>
											<tr>
												<td style="padding: 5px 10px 5px 0; font-weight: 600; color: #555;">Nama</td>
												<td style="padding: 5px 0;">: ${response.nama}</td>
											</tr>
											<tr>
												<td style="padding: 5px 10px 5px 0; font-weight: 600; color: #555;">Alamat</td>
												<td style="padding: 5px 0;">: ${response.alamat}</td>
											</tr>
											<tr>
												<td style="padding: 5px 10px 5px 0; font-weight: 600; color: #555;">Patokan</td>
												<td style="padding: 5px 0;">: ${response.patokan}</td>
											</tr>
											<tr>
												<td style="padding: 5px 10px 5px 0; font-weight: 600; color: #555;">Paket</td>
												<td style="padding: 5px 0;">: ${response.paket}</td>
											</tr>
										</table>
									</div>

									<!-- QR Code -->
									<div style="text-align: center; margin-bottom: 20px;">
										<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; display: inline-block;">
											<img src="data:image/png;base64,${response.qr_base64}"
												 alt="QR Code"
												 style="width: 280px; height: 280px; border: 3px solid #605ca8; padding: 10px; background: white; border-radius: 8px;">
											<p style="margin: 12px 0 0 0; font-size: 13px; color: #666;">
												<i class="fa fa-qrcode"></i> Scan untuk pembayaran via QRIS
											</p>
										</div>
									</div>

									<!-- Rincian Pembayaran -->
									<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;">
										<h4 style="margin: 0 0 12px 0; color: #28a745; font-size: 16px; font-weight: bold;">
											<i class="fa fa-calculator"></i> Rincian Pembayaran
										</h4>
										<table style="width: 100%; font-size: 14px;">
											<tr>
												<td style="padding: 5px 10px 5px 0; width: 180px; color: #555;">Harga Paket</td>
												<td style="padding: 5px 0; text-align: right; font-weight: 600;">Rp ${response.harga_paket}</td>
											</tr>
											<tr>
												<td style="padding: 5px 10px 5px 0; color: #555;">Jumlah Titipan</td>
												<td style="padding: 5px 0; text-align: right; font-weight: 600;">${response.jumlah_titipan !== '-' ? 'Rp ' + response.jumlah_titipan : '-'}</td>
											</tr>
											<tr>
												<td style="padding: 5px 10px 5px 0; color: #555;">Kode Unik</td>
												<td style="padding: 5px 0; text-align: right; font-weight: 600;">Rp ${response.kode_unik}</td>
											</tr>
											<tr style="border-top: 2px solid #28a745;">
												<td style="padding: 12px 10px 8px 0; font-size: 16px; font-weight: bold; color: #28a745;">
													<i class="fa fa-money"></i> TOTAL BAYAR
												</td>
												<td style="padding: 12px 0 8px 0; text-align: right; font-size: 18px; font-weight: bold; color: #28a745;">
													Rp ${response.total_bayar}
												</td>
											</tr>
										</table>
									</div>

									<!-- Keterangan -->
									${response.keterangan !== '-' ? `
									<div style="margin-top: 15px; padding: 12px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
										<strong style="color: #856404;"><i class="fa fa-info-circle"></i> Keterangan:</strong>
										<p style="margin: 5px 0 0 0; color: #856404;">${response.keterangan}</p>
									</div>
									` : ''}

									<!-- Footer Info -->
									<div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 8px; text-align: center; border: 1px solid #b3d7ff;">
										<p style="margin: 0; font-size: 13px; color: #004085;">
											<i class="fa fa-clock-o"></i> Simpan struk ini sebagai bukti pembayaran
										</p>
										<p style="margin: 5px 0 0 0; font-size: 12px; color: #004085;">
											Terima kasih telah menggunakan layanan Wiguna Perkasa Network
										</p>
									</div>
								</div>
							</div>
						`);
					} else {
						$('#qrModalBody').html(`
							<div class="alert alert-danger">
								<i class="fa fa-exclamation-triangle"></i> <strong>Error:</strong> ${response.message}
							</div>
						`);
					}
				},
				error: function() {
					$('#qrModalBody').html(`
						<div class="alert alert-danger">
							<i class="fa fa-exclamation-triangle"></i> <strong>Error:</strong> Gagal memuat QR Code. Silakan coba lagi.
						</div>
					`);
				}
			});
		}
	</script>
</body>

</html>
<?php ob_end_flush(); // Flush output buffer ?>
