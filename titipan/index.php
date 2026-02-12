<?php
	session_start();
	require_once '../inc/koneksi.php';
	require_once '../inc/rupiah.php';

	$password_teknisi = 'berhasil';

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
			$jumlah = intval($_POST['jumlah']);
			$keterangan = $koneksi->real_escape_string($_POST['keterangan'] ?? '');
			$kode_unik = intval(preg_replace('/[^0-9]/', '', $id_pelanggan));
			$bulan = date('m');
			$tahun = date('Y');
			$tgl_titipan = date('Y-m-d');

			$koneksi->query("INSERT INTO tb_titipan (id_pelanggan, bulan, tahun, jumlah, kode_unik, status, tgl_titipan, keterangan)
				VALUES ('$id_pelanggan', '$bulan', '$tahun', $jumlah, $kode_unik, 'BL', '$tgl_titipan', '$keterangan')");

			$alert = "Swal.fire({title: 'Data Titipan Berhasil Disimpan', text: '', icon: 'success', confirmButtonText: 'OKE'});";
		} else {
			$alert = "Swal.fire({title: 'Password Teknisi Salah', text: '', icon: 'error', confirmButtonText: 'OKE'});";
		}
	}

	// Proses Kirim QR Titipan
	if (isset($_GET['kirim_qr'])) {
		$id_titipan = intval($_GET['kirim_qr']);
		$qr_data = $koneksi->query("SELECT t.*, p.nama, p.alamat, p.no_hp, p.no_hp_cadangan
			FROM tb_titipan t
			LEFT JOIN tb_pelanggan p ON t.id_pelanggan = p.id_pelanggan
			WHERE t.id_titipan = $id_titipan AND t.status = 'BL'")->fetch_assoc();

		if ($qr_data) {
			$tagihan_nama = $qr_data['nama'];
			$tagihan_alamat = $qr_data['alamat'];
			$total_bayar = $qr_data['jumlah'] + $qr_data['kode_unik'];
			$tagihan_rupiah = explode(',', rupiah($total_bayar))[0];

			$image_url = "http://localhost/qris-mandiri.jpg";
			$ch_qris = curl_init();
			curl_setopt_array($ch_qris, [
				CURLOPT_URL => "https://qrisku.my.id/api",
				CURLOPT_POST => TRUE,
				CURLOPT_POSTFIELDS => json_encode([
					'amount' => $total_bayar,
					'qris_statis' => '00020101021126690021ID.CO.BANKMANDIRI.WWW01189360000801821981980211718219819800303UMI51440014ID.CO.QRIS.WWW0215ID10254094363220303UMI5204274153033605802ID5922Wiguna Perkasa Network6013Cirebon (Kab)61054516262070703A016304FA5D'
				]),
				CURLOPT_HTTPHEADER => [
					'Content-Type: application/json'
				],
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_FOLLOWLOCATION => TRUE
			]);
			$ch_qris_response = json_decode(curl_exec($ch_qris));
			curl_close($ch_qris);

			if ($ch_qris_response->qris_base64) {
				$image_data = base64_decode($ch_qris_response->qris_base64);
				file_put_contents("../temp_titipan.jpg", $image_data);
				$image_url = "http://localhost/wigunaperkasa.id/temp_titipan.jpg";
			}

			$pesan_titipan =
"Hai kak, ini kami kirimkan kode QR untuk pembayaran *titipan* WiFi-nya.
Mohon bisa segera diselesaikan agar layanan tetap aktif tanpa gangguan.

*Informasi Titipan*
Nominal Titipan	: *$tagihan_rupiah,*
Nama			: *$tagihan_nama,*
Alamat			: *$tagihan_alamat.*
Keterangan		: *" . ($qr_data['keterangan'] ?: '-') . "*

Kalo sudah di transfer, jangan lupa di fotokan atau di screenshot
dan kirim ke kami ya";

			$query_builder = http_build_query([
				'number' => '6287885536663',
				'target' => $qr_data['no_hp'],
				'image_url' => $image_url,
				'message' => $pesan_titipan
			]);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://localhost:3333/api/chat/send-image-message?$query_builder");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Authorization: Bearer a7f5e56c-5235-4b03-8548-6b4000cf2731",
				"Content-Type: application/json",
			]);
			$response_hp1 = curl_exec($ch);
			$error_hp1 = curl_error($ch);
			curl_close($ch);

			$response_hp2 = '';
			if (!empty($qr_data['no_hp_cadangan'])) {
				$query_builder = http_build_query([
					'number' => '6287885536663',
					'target' => $qr_data['no_hp_cadangan'],
					'image_url' => $image_url,
					'message' => $pesan_titipan
				]);

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://localhost:3333/api/chat/send-image-message?$query_builder");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					"Authorization: Bearer a7f5e56c-5235-4b03-8548-6b4000cf2731",
					"Content-Type: application/json",
				]);
				$response_hp2 = curl_exec($ch);
				curl_close($ch);
			}

			// Log untuk debug
			file_put_contents("../titipan_log.txt", date('Y-m-d H:i:s') . "\n"
				. "No HP: " . $qr_data['no_hp'] . "\n"
				. "No HP Cadangan: " . ($qr_data['no_hp_cadangan'] ?? '-') . "\n"
				. "Image URL: " . $image_url . "\n"
				. "Total Bayar: " . $total_bayar . "\n"
				. "QRIS Response: " . json_encode($ch_qris_response) . "\n"
				. "WA Response HP1: " . $response_hp1 . "\n"
				. "WA Error HP1: " . $error_hp1 . "\n"
				. "WA Response HP2: " . $response_hp2 . "\n"
				. "---\n", FILE_APPEND);

			$no_tujuan = $qr_data['no_hp'];
			if (!empty($qr_data['no_hp_cadangan'])) {
				$no_tujuan .= ' & ' . $qr_data['no_hp_cadangan'];
			}
			$alert = "Swal.fire({title: 'QR Titipan Berhasil Dikirim', text: 'Dikirim ke: $no_tujuan', icon: 'success', confirmButtonText: 'OKE'});";
		} else {
			$alert = "Swal.fire({title: 'Data Titipan Tidak Ditemukan', text: '', icon: 'error', confirmButtonText: 'OKE'});";
		}
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
				<div class="card-header">
					<h4 class="card-title mb-0"><b>Titipan Bulanan</b> Wiguna Perkasa Network</h4>
				</div>
				<div class="card-body">
					<?php if ($is_teknisi == false) { ?>
					<form method="GET" class="row">
						<div class="col-md-6 form-group">
							<label for="password">Password Teknisi</label>
							<input type="password" name="password" id="password" class="form-control" placeholder="Password Teknisi">
						</div>
						<div class="col-md-12">
							<button type="submit" class="btn btn-primary">Masuk Sebagai Teknisi</button>
							<button type="reset" class="btn btn-secondary" style="margin-left: 15px;">Ulangi</button>
						</div>
					</form>
					<?php } else { ?>

					<!-- Form Tambah Titipan -->
					<form method="POST" id="formTitipan">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Cari Pelanggan</label>
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
											data-tarif="<?= $plg['tarif'] ?? 0 ?>">
											<?= $plg['id_pelanggan'] ?> - <?= $plg['nama'] ?>
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
									<label>Harga</label>
									<input type="text" class="form-control" id="txt_tarif" readonly>
								</div>
								<div class="form-group">
									<label>Jumlah Titipan (Rp)</label>
									<input type="number" class="form-control" name="jumlah" placeholder="Masukkan jumlah titipan" required>
								</div>
								<div class="form-group">
									<label>Keterangan</label>
									<input type="text" class="form-control" name="keterangan" placeholder="Keterangan (opsional)">
								</div>
								<div class="form-group">
									<label for="password">Password Teknisi</label>
									<input type="password" name="password" class="form-control" placeholder="Password Teknisi" required>
								</div>
								<button type="submit" name="simpan_titipan" value="1" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Titipan</button>
							</div>
						</div>
					</form>

					<hr style="margin: 30px 0;">

					<!-- Tabel Data Titipan (Hanya Belum Lunas) -->
					<h4><i class="fa fa-table"></i> Data Titipan (Belum Lunas)</h4>
					<div class="table-responsive" style="margin-top: 15px;">
						<table id="example1" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No</th>
									<th>ID Pelanggan</th>
									<th>Nama Pelanggan</th>
									<th>Alamat</th>
									<th>Patokan</th>
									<th>Paket</th>
									<th>Harga</th>
									<th>Jumlah Titipan</th>
									<th>Keterangan</th>
									<th>Tanggal</th>
									<th>Aksi</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$no = 1;
									$sql_titipan = $koneksi->query("SELECT t.*, p.nama, p.alamat, p.patokan, p.no_hp, p.no_hp_cadangan, pk.paket, pk.tarif
										FROM tb_titipan t
										LEFT JOIN tb_pelanggan p ON t.id_pelanggan = p.id_pelanggan
										LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket
										WHERE t.status = 'BL'
										ORDER BY t.id_titipan DESC");
									if ($sql_titipan) {
										while ($data = $sql_titipan->fetch_assoc()) {
								?>
								<tr>
									<td><?= $no++ ?></td>
									<td><?= $data['id_pelanggan'] ?></td>
									<td><?= $data['nama'] ?></td>
									<td><?= $data['alamat'] ?></td>
									<td><?= $data['patokan'] ?? '-' ?></td>
									<td><?= $data['paket'] ?? '-' ?></td>
									<td><?= isset($data['tarif']) ? 'Rp ' . number_format($data['tarif'], 0, ',', '.') : '-' ?></td>
									<td>Rp <?= number_format($data['jumlah'], 0, ',', '.') ?></td>
									<td><?= $data['keterangan'] ?? '-' ?></td>
									<td><?= $data['tgl_titipan'] ?></td>
									<td>
										<a href="?kirim_qr=<?= $data['id_titipan'] ?>" onclick="return confirm('Kirim QR titipan via WhatsApp ke <?= $data['nama'] ?>?\nNo HP: <?= $data['no_hp'] ?><?= !empty($data['no_hp_cadangan']) ? ' & ' . $data['no_hp_cadangan'] : '' ?>')" title="Kirim QR via WhatsApp" class="btn btn-primary btn-sm">
											<i class="fa fa-qrcode"></i>
										</a>
									</td>
								</tr>
								<?php
										}
									}
								?>
							</tbody>
						</table>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery 2.2.3 -->
	<script src="../plugins/jQuery/jquery-2.2.3.min.js"></script>
	<!-- Bootstrap 3.3.6 -->
	<script src="../bootstrap/js/bootstrap.min.js"></script>
	<!-- Sweet Alert 2 -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script src="../plugins/select2/select2.full.min.js"></script>
	<!-- DataTables -->
	<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>

	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js"></script>
	<script src="../dist/js/demo.js"></script>

	<script>
		<?= empty($alert) ? '' : $alert ?>

		$(function() {
			$("#example1").DataTable({
				"lengthMenu": [[10, 25, 50, 100, 1000], [10, 25, 50, 100, 1000]]
			});
			$(".select2").select2();
		});

		$('#id_pelanggan').on('change', function() {
			var selected = $(this).find(':selected');
			$('#txt_id_pelanggan').val(selected.val());
			$('#txt_nama').val(selected.data('nama'));
			$('#txt_alamat').val(selected.data('alamat'));
			$('#txt_patokan').val(selected.data('patokan'));
			$('#txt_paket').val(selected.data('paket'));
			var tarif = selected.data('tarif');
			$('#txt_tarif').val(tarif ? 'Rp ' + Number(tarif).toLocaleString('id-ID') : '');
		});
	</script>
</body>

</html>
