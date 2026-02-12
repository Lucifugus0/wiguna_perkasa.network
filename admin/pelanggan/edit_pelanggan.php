<?php

    if(isset($_GET['kode'])){
        $sql_cek = "SELECT * FROM tb_pelanggan WHERE id_pelanggan='".$_GET['kode']."'";
        $query_cek = mysqli_query($koneksi, $sql_cek);
        $data_cek = mysqli_fetch_array($query_cek,MYSQLI_BOTH);
    }
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<!-- general form elements -->
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title">Ubah Data paket</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse">
							<i class="fa fa-minus"></i>
						</button>
						<button type="button" class="btn btn-box-tool" data-widget="remove">
							<i class="fa fa-remove"></i>
						</button>
					</div>
				</div>
				<!-- /.box-header -->
				<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">ID Pelanggan</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="id_pelanggan" name="id_pelanggan" value="<?php echo $data_cek['id_pelanggan']; ?>"
								 readonly/>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">E-Mail</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="email" name="email" value="<?php echo $data_cek['email']; ?>">
							</div>
						</div>


						<div class="form-group">
							<label class="col-sm-2 control-label">Password</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="password" name="password" value="<?php echo $data_cek['password']; ?>">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Nama</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="nama" name="nama" value="<?php echo $data_cek['nama']; ?>">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Mac / SN Modem</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="mac_modem" name="mac_modem" value="<?php echo $data_cek['mac_modem']; ?>">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">IP Modem</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="ip_modem" name="ip_modem" value="<?php echo $data_cek['ip_modem']; ?>">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Teknisi</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="teknisi" name="teknisi" value="<?php echo $data_cek['teknisi']; ?>">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Titik ODP</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="titik_odp" name="titik_odp" value="<?php echo $data_cek['titik_odp']; ?>">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Alamat</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo $data_cek['alamat']; ?>">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Patokan</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="patokan" name="patokan" placeholder="Patokan Rumah Pelanggan" value="<?php echo $data_cek['patokan']; ?>" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">No HP Utama</label>
							<div class="col-sm-4">
								<input type="number" class="form-control" id="no_hp" name="no_hp" value="<?php echo $data_cek['no_hp']; ?>">
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">No HP Cadangan</label>
							<div class="col-sm-4">
								<input type="number" class="form-control" id="no_hp_cadangan" name="no_hp_cadangan" value="<?php echo $data_cek['no_hp_cadangan']; ?>">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Paket</label>
							<div class="col-sm-4">
								<select name="id_paket" id="id_paket" class="form-control select2" style="width: 100%;">
									<option selected="">- Pilih -</option>
									<?php
									// ambil data dari database
									$query = "select * from tb_paket";
									$hasil = mysqli_query($koneksi, $query);
									while ($row = mysqli_fetch_array($hasil)) {
									?>
									<option value="<?php echo $row['id_paket'] ?>" <?=$data_cek[
									 'id_paket']==$row[ 'id_paket'] ? "selected" : null ?>>
										<?php echo $row['paket'] ?>
										|
										<?php echo $row['tarif'] ?>
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
								<input type="text" class="form-control" id="hp_otomatis" name="hp_otomatis" value="<?= $data_cek['hp_otomatis'] ?>" placeholder="HP Otomatis" required>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal Pemasangan</label>
							<div class="col-sm-4">
								<input type="date" class="form-control" id="tanggal_pemasangan" name="tanggal_pemasangan" value="<?php echo $data_cek['tanggal_pemasangan']; ?>">
							</div>
						</div>

						<!-- /.box-body -->
						<div class="box-footer">
							<a href="?page=data-pelanggan" class="btn btn-default">Batal</a>
							<input type="submit" name="Ubah" value="Simpan" class="btn btn-success">
						</div>
				</form>
				</div>
				<!-- /.box -->
</section>

<?php

if (isset ($_POST['Ubah'])){
	$_POST['hp_otomatis'] = !$_POST['hp_otomatis'] ? "0" : $_POST['hp_otomatis'];

	$exist_hotspot_user = $API->comm("/ip/hotspot/user/print", [
		'?name' => $_POST['email']
	]);
	
	if ($_POST['email'] != $data_cek['email'] && count($exist_hotspot_user) >= 1) {
		echo "<script>
		Swal.fire({title: 'Ubah Data Gagal - E-Mail Sudah Digunakan',text: '',icon: 'error',confirmButtonText: 'OK'
		}).then((result) => {
			if (result.value) {
				window.location = 'index.php?page=data-pelanggan';
			}
		})</script>";
	} else {
		$hotspot_user = $API->comm("/ip/hotspot/user/print", [
			'?name' => $data_cek['email']
		]);
		
		//mulai Mahasiswaoses ubah
		$sql_ubah = "UPDATE tb_pelanggan SET
			nama='".$_POST['nama']."',
			mac_modem='".$_POST['mac_modem']."',
			ip_modem='".$_POST['ip_modem']."',
			teknisi='".$_POST['teknisi']."',
			titik_odp='".$_POST['titik_odp']."',
			alamat='".$_POST['alamat']."',
			patokan='".$_POST['patokan']."',
			no_hp='".$_POST['no_hp']."',
			no_hp_cadangan='".$_POST['no_hp_cadangan']."',
			email='".$_POST['email']."',
			password='".$_POST['password']."',
			id_paket='".$_POST['id_paket']."',
			hp_otomatis='".$_POST['hp_otomatis']."',
			tanggal_pemasangan='".$_POST['tanggal_pemasangan']."'
			WHERE id_pelanggan='".$_POST['id_pelanggan']."'";
		$query_ubah = mysqli_query($koneksi, $sql_ubah);

		if ($query_ubah) {
			$id_paket = $_POST['id_paket'];
			$cek_paket = mysqli_query($koneksi, "SELECT * FROM tb_paket WHERE id_paket = '$id_paket'");
			$data_paket = $cek_paket->fetch_assoc();
			
			if (isset($hotspot_user[0]) && !empty($hotspot_user[0])) {
				$API->comm("/ip/hotspot/user/set", [
					'.id' => $hotspot_user[0]['.id'],
					'name' => $_POST['email']
				]);
				$API->comm("/ip/hotspot/user/set", [
					'.id' => $hotspot_user[0]['.id'],
					'password' => $_POST['password']
				]);
				$API->comm("/ip/hotspot/user/set", [
					'.id' => $hotspot_user[0]['.id'],
					'profile' => $data_paket['name']
				]);
			}
			
			mysqli_query($koneksi, "UPDATE tb_tagihan SET tagihan = '".$data_paket['tarif']."' WHERE bulan = '".date("m")."' AND tahun = '".date("Y")."' AND status = 'BL' AND id_pelanggan = '".$_POST['id_pelanggan']."'");
			
			$aktifitas = "UBAH PELANGGAN".PHP_EOL.PHP_EOL
				."Nama: ".$data_cek['nama']." ke ".$_POST['nama'].PHP_EOL
				."MAC Modem: ".$data_cek['mac_modem']." ke ".$_POST['mac_modem'].PHP_EOL
				."Alamat: ".$data_cek['alamat']." ke ".$_POST['alamat'].PHP_EOL
				."Patokan: ".$data_cek['Patokan']." ke ".$_POST['patokan'].PHP_EOL
				."No HP Utama: ".$data_cek['no_hp']." ke ".$_POST['no_hp'].PHP_EOL
				."No HP Cadangan: ".$data_cek['no_hp_cadangan']." ke ".$_POST['no_hp_cadangan'].PHP_EOL
				."E-Mail: ".$data_cek['email']." ke ".$_POST['email'].PHP_EOL
				."Password: ".$data_cek['password']." ke ".$_POST['password'].PHP_EOL
				."Paket: ".$data_cek['id_paket']." ke ".$_POST['id_paket'].PHP_EOL
				."Tanggal Pemasangan: ".$data_cek['tanggal_pemasangan']." ke ".$_POST['tanggal_pemasangan'];
			mysqli_query($koneksi, "INSERT INTO tb_log (pengguna, aktifitas) VALUES ('".$_SESSION['ses_nama']."', '".$aktifitas."')");
			
			echo "<script>
			Swal.fire({title: 'Ubah Data Berhasil',text: '',icon: 'success',confirmButtonText: 'OK'
			}).then((result) => {
				if (result.value) {
					window.location = 'index.php?page=data-pelanggan';
				}
			})</script>";
			}else{

			echo "<script>
			Swal.fire({title: 'Ubah Data Gagal',text: '',icon: 'error',confirmButtonText: 'OK'
			}).then((result) => {
				if (result.value) {
					window.location = 'index.php?page=data-pelanggan';
				}
			})</script>";
		}
	}
}

