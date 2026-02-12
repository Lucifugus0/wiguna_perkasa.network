<?php
	session_start();
	require_once '../inc/routerosapi.class.php';
	require_once '../inc/koneksi.php';
	
	$API = new RouterosAPI();
	$API->port = $router_port;
	if (!$API->connect($router_host, $router_username, $router_password)) {
		die("Tidak dapat terhubung ke Mikrotik!");
	}
	
	$check_paket = $koneksi->query("SELECT * FROM tb_paket ORDER BY id_paket ASC");
	
	$carikode = mysqli_query($koneksi, "SELECT id_pelanggan FROM tb_pelanggan ORDER BY id_pelanggan DESC LIMIT 1");
	if ($carikode && mysqli_num_rows($carikode) > 0) {
		$datakode = mysqli_fetch_array($carikode);
		$kode = $datakode['id_pelanggan'];
		$urut = substr($kode, 1, 3);
		$tambah = (int)$urut + 1;
	} else {
		$tambah = 1;
	}

	if (strlen($tambah) == 1) {
		$id_pelanggan = "C" . "00" . $tambah;
	} elseif (strlen($tambah) == 2) {
		$id_pelanggan = "C" . "0" . $tambah;
	} elseif (strlen($tambah) == 3) {
		$id_pelanggan = "C" . $tambah;
	} else {
		$id_pelanggan = "C" . $tambah;
	}
	
	$password_teknisi = 'berhasil';
	
	$is_teknisi = isset($_SESSION['is_teknisi_allures']) ? true : false;
	if (isset($_GET['password']) && $_GET['password'] == $password_teknisi) {
		$_SESSION['is_teknisi_allures'] = true;
		header("Location: index.php");
	}
	
	if ($_POST) {
		if (isset($_POST['password']) && @$_POST['password'] == $password_teknisi) {
			$exists_hotspot_user = $API->comm("/ip/hotspot/user/print", [
				'?name' => $_POST['email']
			]);
			
			if (count($exists_hotspot_user) == 0) {
				$cek_id_pelanggan = mysqli_query($koneksi, "SELECT * FROM tb_pelanggan WHERE id_pelanggan = '".$_POST['id']."'");
				
				if (mysqli_num_rows($cek_id_pelanggan) == 0) {
					$_POST['hp_otomatis'] = !$_POST['hp_otomatis'] ? "0" : $_POST['hp_otomatis'];
					
					$sql_simpan = "INSERT INTO tb_pelanggan (id_pelanggan, nama, mac_modem, ip_modem, teknisi, titik_odp, alamat, patokan, no_hp, no_hp_cadangan, email, password, id_paket, hp_otomatis, tanggal_pemasangan) VALUES (
					   '".$_POST['id']."',
					  '".$_POST['nama']."',
					  '".$_POST['mac_modem']."',
					  '".$_POST['ip_modem']."',
					  '".$_POST['teknisi']."',
					  '".$_POST['titik_odp']."',
					  '".$_POST['alamat']."',
					  '".$_POST['patokan']."',
					  '".$_POST['no_hp']."',
					  '".$_POST['no_hp_cadangan']."',
					  '".$_POST['email']."',
					  '".$_POST['password_pelanggan']."',
					  '".$_POST['paket']."',
					  '".$_POST['hp_otomatis']."',
					  '".$_POST['tanggal_pemasangan']."')";
					
					$insert = $koneksi->query($sql_simpan);
					if ($insert) {
						$id_paket = $_POST['paket'];
						$cek_paket = mysqli_query($koneksi, "SELECT * FROM tb_paket WHERE id_paket = '$id_paket'");
						$data_paket = $cek_paket->fetch_assoc();
						
						$API->comm("/ip/hotspot/user/add", [
							'name' => $_POST['email'],
							'password' => $_POST['password_pelanggan'],
							'comment' => $_POST['id'] . ' ' . $_POST['nama'],
							'profile' => $data_paket['name']
						]);
						$API->disconnect();
						
						unset($_POST);
						$alert = "Swal.fire({title: 'Tambah Data Berhasil', text: '', icon: 'success', confirmButtonText: 'OKE'});";
					} else {
						$alert = "Swal.fire({title: 'Tambah Data Gagal', text: '', icon: 'error', confirmButtonText: 'OKE'});";
					}
				} else {
					$alert = "Swal.fire({title: 'Tambah Data Gagal - ID Pelanggan Sudah Digunakan', text: '', icon: 'error', confirmButtonText: 'OKE'});";
				}
			} else {
				$alert = "Swal.fire({title: 'Tambah Data Gagal - E-Mail Sudah Digunakan', text: '', icon: 'error', confirmButtonText: 'OKE'});";
			}
		} else {
			$alert = "Swal.fire({title: 'Password Teknisi Salah', text: '', icon: 'error', confirmButtonText: 'OKE'});";
		}
	}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>TAMBAH PELANGGAN WIGUNA PERKASA NETWORK</title>
	<link rel="icon" href="../dist/img/komp.png">
	<!-- Tell the browser to be responsive to screen width -->
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
	<!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</head>

<body class="hold-transition skin-purple">
	<div class="row" style="margin: 20px;">
		<div class="col-md-10 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title mb-0"><b>Tambah Pelanggan</b> Wiguna Perkasa Network</h4>
					<?php if ($is_teknisi) { ?>
					<button type="button" class="btn btn-success btn-sm" style="margin-top: 10px;" onclick="copyData()"><i class="fa fa-copy"></i> Copy Data</button>
					<?php } ?>
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
					<!-- PASTE DATA DARI WHATSAPP -->
					<div class="form-group" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #eee;">
						<label><i class="fa fa-whatsapp" style="color:#25D366;"></i> Paste Data dari WhatsApp</label>
						<textarea id="paste_area" class="form-control" rows="12" placeholder="Paste teks dari WhatsApp di sini...&#10;&#10;Contoh:&#10;Formulir pendaftaran pelanggan...&#10;Nama Pelanggan: WIGUNA PERKASA&#10;Mac Modem: xx&#10;...&#10;&#10;&#10;PASTIKAN HURUF KAPITAL BESAR..."></textarea>
						<button type="button" class="btn btn-info btn-sm" style="margin-top: 10px;" onclick="parseData()"><i class="fa fa-magic"></i> Cek Data</button>
						<button type="button" class="btn btn-default btn-sm" style="margin-top: 10px; margin-left: 5px;" onclick="document.getElementById('paste_area').value = ''"><i class="fa fa-eraser"></i> Bersihkan</button>
					</div>
					<form method="POST" class="row">
						<div class="col-md-12 form-group">
							<label for="id">ID Pelanggan</label>
							<input type="text" name="id" id="id" class="form-control" value="<?= isset($_POST['id']) ? $_POST['id'] : $id_pelanggan ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="nama">Nama Pelanggan</label>
							<input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Pelanggan" value="<?= @$_POST['nama'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="mac_modem">MAC / SN Modem</label>
							<input type="text" name="mac_modem" id="mac_modem" class="form-control" placeholder="MAC / SN Modem" value="<?= @$_POST['mac_modem'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="ip_modem">IP Modem</label>
							<input type="text" name="ip_modem" id="ip_modem" class="form-control" placeholder="IP Modem" value="<?= @$_POST['ip_modem'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="teknisi">Teknisi</label>
							<input type="text" name="teknisi" id="teknisi" class="form-control" placeholder="Teknisi" value="<?= @$_POST['teknisi'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="titik_odp">Titik ODP</label>
							<input type="text" name="titik_odp" id="titik_odp" class="form-control" placeholder="Titik ODP" value="<?= @$_POST['titik_odp'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="alamat">Alamat</label>
							<input type="text" name="alamat" id="alamat" class="form-control" placeholder="Alamat" value="<?= @$_POST['alamat'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="patokan">Patokan</label>
							<input type="text" name="patokan" id="patokan" class="form-control" placeholder="Patokan" value="<?= @$_POST['patokan'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="no_hp">No HP Utama</label>
							<input type="number" name="no_hp" id="no_hp" class="form-control" placeholder="No HP Utama Diawali 62" value="<?= @$_POST['no_hp'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="no_hp">No HP Cadangan</label>
							<input type="number" name="no_hp_cadangan" id="no_hp_cadangan" class="form-control" placeholder="No HP Cadangan Diawali 62" value="<?= @$_POST['no_hp'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="text">E-Mail</label>
							<input type="text" name="email" id="email" class="form-control" placeholder="E-Mail" value="<?= @$_POST['email'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="password_pelanggan">Password</label>
							<input type="text" name="password_pelanggan" id="password_pelanggan" class="form-control" placeholder="Password" value="<?= @$_POST['password_pelanggan'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="paket">Paket</label>
							<select name="paket" id="paket" class="form-control">
								<option value="">Pilih Salah Satu</option>
								<?php while ($data_paket = $check_paket->fetch_assoc()) { ?>
								<option value="<?= $data_paket['id_paket'] ?>" <?= $data_paket['id_paket'] == @$_POST['paket'] ? 'selected' : '' ?>><?= $data_paket['paket'] ?> | Rp <?= number_format($data_paket['tarif'], 0, ',', '.') ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-6 form-group">
							<label for="no_hp">HP Otomatis</label>
							<input type="text" name="hp_otomatis" id="hp_otomatis" class="form-control" placeholder="HP Otomatis" value="<?= @$_POST['hp_otomatis'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="text">Tanggal Pemasangan</label>
							<input type="date" name="tanggal_pemasangan" id="tanggal_pemasangan" class="form-control" placeholder="E-Mail" value="<?= @$_POST['tanggal_pemasangan'] ?>">
						</div>
						<div class="col-md-6 form-group">
							<label for="password">Password Teknisi</label>
							<input type="password" name="password" id="password" class="form-control" placeholder="Password Teknisi">
						</div>
						<div class="col-md-12">
							<button type="submit" class="btn btn-primary">Simpan</button>
							<button type="reset" class="btn btn-secondary" style="margin-left: 15px;">Ulangi</button>
						</div>
					</form>
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
	<!-- AdminLTE for demo purposes -->
	<script src="../dist/js/demo.js"></script>
	<!-- page script -->
	<script>
		<?= empty($alert) ? '' : $alert ?>

		function parseData() {
			var text = document.getElementById('paste_area').value.trim();
			if (!text) {
				Swal.fire('Kosong', 'Silakan paste teks dari WhatsApp terlebih dahulu.', 'warning');
				return;
			}

			var lines = text.split('\n');
			var parsed = {};
			var filled = 0;

			var mappings = [
				{ keywords: ['nama pelanggan'], id: 'nama' },
				{ keywords: ['mac modem', 'sn modem'], id: 'mac_modem' },
				{ keywords: ['alamat ip', 'ip modem'], id: 'ip_modem' },
				{ keywords: ['teknisi'], id: 'teknisi' },
				{ keywords: ['titik odp'], id: 'titik_odp' },
				{ keywords: ['alamat'], id: 'alamat' },
				{ keywords: ['patokan'], id: 'patokan' },
				{ keywords: ['no wa cadangan', 'no hp cadangan'], id: 'no_hp_cadangan' },
				{ keywords: ['no wa', 'no hp'], id: 'no_hp' },
				{ keywords: ['username', 'e-mail', 'email'], id: 'email' },
				{ keywords: ['password'], id: 'password_pelanggan' },
				{ keywords: ['tanggal.pemasangan', 'tanggal pemasangan', 'tgl pemasangan'], id: 'tanggal_pemasangan' },
				{ keywords: ['hp otomatis'], id: 'hp_otomatis' }
			];

			for (var i = 0; i < lines.length; i++) {
				var line = lines[i].trim();
				if (!line) continue;

				var sepIndex = line.indexOf(':');
				if (sepIndex === -1) continue;

				var label = line.substring(0, sepIndex).trim().toLowerCase();
				var value = line.substring(sepIndex + 1).trim();

				for (var j = 0; j < mappings.length; j++) {
					var map = mappings[j];
					if (parsed[map.id]) continue;

					for (var k = 0; k < map.keywords.length; k++) {
						if (label.indexOf(map.keywords[k]) !== -1) {
							parsed[map.id] = value;
							break;
						}
					}
				}
			}

			for (var fieldId in parsed) {
				var el = document.getElementById(fieldId);
				if (el && parsed[fieldId]) {
					el.value = parsed[fieldId];
					filled++;
				}
			}

			if (filled > 0) {
				Swal.fire({
					title: 'Parse Berhasil!',
					text: filled + ' field berhasil diisi otomatis.',
					icon: 'success',
					confirmButtonText: 'OKE'
				});
			} else {
				Swal.fire({
					title: 'Tidak Ada Data',
					text: 'Format teks tidak dikenali. Pastikan format sesuai template WhatsApp.',
					icon: 'warning',
					confirmButtonText: 'OKE'
				});
			}
		}

		function copyData() {
			var fields = [
				{ label: 'Formulir  pendaftaran pelanggan PT. WIGUNA PERKASA NETWORK' },
				{ label: 'ID Pelanggan', id: 'id' },
				{ label: 'Nama Pelanggan', id: 'nama' },
				{ label: 'MAC / SN Modem', id: 'mac_modem' },
				{ label: 'IP Modem', id: 'ip_modem' },
				{ label: 'Teknisi', id: 'teknisi' },
				{ label: 'Titik ODP', id: 'titik_odp' },
				{ label: 'Alamat', id: 'alamat' },
				{ label: 'Patokan', id: 'patokan' },
				{ label: 'No HP Utama', id: 'no_hp' },
				{ label: 'No HP Cadangan', id: 'no_hp_cadangan' },
				{ label: 'E-Mail', id: 'email' },
				{ label: 'Password', id: 'password_pelanggan' },
				{ label: 'Paket', id: 'paket', isSelect: true },
				{ label: 'HP Otomatis', id: 'hp_otomatis' },
				{ label: 'Tanggal Pemasangan', id: 'tanggal_pemasangan' },
				{ label: 'Status AKTIF' }
			];

			var textLines = [];
			var htmlLines = [];

			for (var i = 0; i < fields.length; i++) {
				var el = document.getElementById(fields[i].id);
				var value = '';
				if (fields[i].isSelect && el) {
					value = el.options[el.selectedIndex] ? el.options[el.selectedIndex].text : '';
					if (el.value === '') value = '-';
				} else if (el) {
					value = el.value || '-';
				}
				textLines.push(fields[i].label + ' : ' + value);
				htmlLines.push('<b>' + fields[i].label + '</b> : ' + value);
			}

			var textData = textLines.join('\n');
			var htmlData = htmlLines.join('<br>');

			Swal.fire({
				title: 'Data Pelanggan',
				html: '<div style="text-align:left; font-size:14px; line-height:1.8;">' + htmlData + '</div>',
				showCancelButton: true,
				confirmButtonText: '<i class="fa fa-copy"></i> Salin',
				cancelButtonText: 'Tutup',
				confirmButtonColor: '#28a745',
				width: '550px'
			}).then(function(result) {
				if (result.isConfirmed) {
					var temp = document.createElement('textarea');
					temp.value = textData;
					document.body.appendChild(temp);
					temp.select();
					document.execCommand('copy');
					document.body.removeChild(temp);
					Swal.fire({
						title: 'Berhasil Disalin!',
						text: 'Data pelanggan telah disalin ke clipboard.',
						icon: 'success',
						timer: 1500,
						showConfirmButton: false
					});
				}
			});
		}
	</script>
</body>

</html>