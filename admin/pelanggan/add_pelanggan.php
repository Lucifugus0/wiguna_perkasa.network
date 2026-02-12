<?php
$pass_acak = mt_rand(1000, 9999);

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
    $format = "C" . "00" . $tambah;
} elseif (strlen($tambah) == 2) {
    $format = "C" . "0" . $tambah;
} elseif (strlen($tambah) == 3) {
    $format = "C" . $tambah;
} else {
    $format = "C" . $tambah;
}

?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">TAMBAH PELANGGAN</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse">
							<i class="fa fa-minus"></i>
						</button>
						<button type="button" class="btn btn-box-tool" data-widget="remove">
							<i class="fa fa-remove"></i>
						</button>
					</div>
				</div>

				<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">ID Pelanggan</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="id_pelanggan" name="id_pelanggan" value="<?php echo $format; ?>" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Nama</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="nama" name="nama" value="<?= @$_GET['nama'] ?>" placeholder="Nama Lengkap" autofocus required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Mac / SN Modem</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="mac_modem" name="mac_modem" value="<?= @$_GET['mac_modem'] ?>" placeholder="Mac / SN Modem" autofocus required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">IP Modem</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="ip_modem" name="ip_modem" value="<?= @$_GET['ip_modem'] ?>" placeholder="IP Modem" autofocus required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Teknisi</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="teknisi" name="teknisi" value="<?= @$_GET['teknisi'] ?>" placeholder="Teknisi" autofocus required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Titik ODP</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="titik_odp" name="titik_odp" value="<?= @$_GET['titik_odp'] ?>" placeholder="Titik ODP" autofocus required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Alamat</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="alamat" name="alamat" value="<?= @$_GET['alamat'] ?>" placeholder="Alamat" required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Patokan</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="patokan" name="patokan" value="<?= @$_GET['patokan'] ?>" placeholder="Patokan Rumah Pelanggan" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">No HP Utama</label>
							<div class="col-sm-4">
								<input type="number" class="form-control" id="no_hp" name="no_hp" value="<?= @$_GET['no_hp'] ?>" placeholder="No HP Utama Di Awali 62" required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">No HP Cadangan</label>
							<div class="col-sm-4">
								<input type="number" class="form-control" id="no_hp_cadangan" name="no_hp_cadangan" value="<?= @$_GET['no_hp_cadangan'] ?>" placeholder="No HP Cadangan Di Awali 62" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">E-Mail</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="email" name="email" value="<?= @$_GET['email'] ?>" placeholder="E-Mail" required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Password</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="password" name="password" value="<?= @$_GET['password'] ?>" placeholder="Password" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Paket</label>
							<div class="col-sm-4">
								<select name="id_paket" id="id_paket" class="form-control select2" style="width: 100%;">
									<option selected="<?= @$_GET['id_paket'] == '' ? 'selected' : '' ?>">-- Pilih --</option>
									<?php
								// ambil data dari database
								$query = "select * from tb_paket";
								$hasil = mysqli_query($koneksi, $query);
								while ($row = mysqli_fetch_array($hasil)) {
								?>
									<option value="<?php echo $row['id_paket'] ?>" selected="<?= @$_GET['id_paket'] == '' ? 'selected' : '' ?>">
										<?php echo $row['paket'] ?>
										|
										<?php echo rupiah($row['tarif']); ?>
									</option>
									<?php
								}
								?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">HP Otomatis</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="hp_otomatis" name="hp_otomatis" value="<?= @$_GET['hp_otomatis'] ?>" placeholder="HP Otomatis" required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal Pemasangan</label>
							<div class="col-sm-4">
								<input type="date" class="form-control" id="tanggal_pemasangan" name="tanggal_pemasangan" value="<?= @$_GET['tanggal_pemasangan'] ?>" placeholder="Tanggal Pemasangan" required>
							</div>
						</div>

						<!-- /.box-body -->
						<div class="box-footer">
							<a href="?page=data-pelanggan" class="btn btn-default">Batal</a>
							<input type="submit" name="Simpan" value="Simpan" class="btn btn-primary">
						</div>
				</form>
				</div>
			</div>
		</div>
</section>

<?php
    if (isset ($_POST['Simpan'])){
		$_POST['hp_otomatis'] = !$_POST['hp_otomatis'] ? "0" : $_POST['hp_otomatis'];

		$exists_hotspot_user = $API->comm("/ip/hotspot/user/print", [
			'?name' => $_POST['email']
		]);
    
		if (count($exists_hotspot_user) == 0) {
			$cek_id_pelanggan = mysqli_query($koneksi, "SELECT * FROM tb_pelanggan WHERE id_pelanggan = '".$_POST['id_pelanggan']."'");
			
			if (mysqli_num_rows($cek_id_pelanggan) == 0) {
				$sql_simpan = "INSERT INTO tb_pelanggan (id_pelanggan, nama, mac_modem, ip_modem, teknisi, titik_odp, alamat, patokan, no_hp, no_hp_cadangan, email, password, id_paket, hp_otomatis, tanggal_pemasangan) VALUES (
				   '".$_POST['id_pelanggan']."',
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
				  '".$_POST['password']."',
				  '".$_POST['id_paket']."',
				  '".$_POST['hp_otomatis']."',
				  '".$_POST['tanggal_pemasangan']."')";
				$query_simpan = mysqli_query($koneksi, $sql_simpan);

				if ($query_simpan){
				  $id_paket = $_POST['id_paket'];
				  $cek_paket = mysqli_query($koneksi, "SELECT * FROM tb_paket WHERE id_paket = '$id_paket'");
				  $data_paket = $cek_paket->fetch_assoc();
				  
				  $API->comm("/ip/hotspot/user/add", [
					'name' => $_POST['email'],
					'password' => $_POST['password'],
					'comment' => $_POST['id_pelanggan'] . ' ' . $_POST['nama'],
					'profile' => $data_paket['name']
				  ]);
				  $API->disconnect();
				  
				  echo "<script>
				  Swal.fire({title: 'Tambah Data Berhasil',text: '',icon: 'success',confirmButtonText: 'OKE'
				  }).then((result) => {
					  if (result.value) {
						  window.location = 'index.php?page=data-pelanggan';
					  }
				  })</script>";
				}else{
				  echo "<script>
				  Swal.fire({title: 'Tambah Data Gagal',text: '',icon: 'error',confirmButtonText: 'OKE'
				  }).then((result) => {
					  if (result.value) {
						  window.location = 'index.php?page=data-pelanggan';
					  }
				  })</script>";
				}
			} else {
				echo "<script>
				Swal.fire({title: 'Tambah Data Gagal - ID Pelanggan Sudah Digunakan',text: '',icon: 'error',confirmButtonText: 'OKE'
				}).then((result) => {
				if (result.value) {
				window.location = 'index.php?page=data-pelanggan';
				}
				})</script>";
			}
		} else {
			echo "<script>
			Swal.fire({title: 'Tambah Data Gagal - E-Mail Sudah Digunakan',text: '',icon: 'error',confirmButtonText: 'OKE'
			}).then((result) => {
			if (result.value) {
			window.location = 'index.php?page=data-pelanggan';
			}
			})</script>";
		}
	}
    
