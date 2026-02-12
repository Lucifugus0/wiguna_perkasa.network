<?php

date_default_timezone_set("Asia/Jakarta");
if(isset($_GET['kode'])){
	$cek_data = mysqli_query($koneksi, "SELECT * FROM tb_pelanggan WHERE id_pelanggan='".$_GET['kode']."'");
	$data = $cek_data->fetch_assoc();
	$sql_hapus = "DELETE FROM tb_pelanggan WHERE id_pelanggan='".$_GET['kode']."'";
	$query_hapus = mysqli_query($koneksi, $sql_hapus);

	if ($query_hapus) {
		$tanggal_off = date("Y-m-d H:i:s");
		mysqli_query($koneksi, "INSERT INTO tb_pelanggan_off VALUES ('', '{$data['nama']}', '{$data['mac_modem']}', '{$data['teknisi']}', '{$data['titik_odp']}', '{$data['alamat']}', '{$data['patokan']}', '{$data['no_hp']}', '{$data['no_hp_cadangan']}', '{$data['email']}', '{$data['password']}', '{$data['level']}', '{$data['id_paket']}', '{$data['tanggal_pemasangan']}', '$tanggal_off')");
		$aktifitas = "HAPUS PELANGGAN".PHP_EOL.PHP_EOL
			."ID Pelanggan: ".$_GET['kode'].PHP_EOL
			."Nama: ".$data['nama'];
		mysqli_query($koneksi, "INSERT INTO tb_log (pengguna, aktifitas) VALUES ('".$_SESSION['ses_nama']."', '".$aktifitas."')");
		
		$hotspot_user = $API->comm("/ip/hotspot/user/print", [
			'?name' => $data['email']
		]);
		if (isset($hotspot_user[0]) && !empty($hotspot_user[0])) {
			$API->comm("/ip/hotspot/user/remove", [
				'.id' => $hotspot_user[0]['.id']
			]);
		}
		$API->disconnect();
		
		echo "<script>
		Swal.fire({title: 'Hapus Data Berhasil',text: '',icon: 'success',confirmButtonText: 'OK'
		}).then((result) => {
			if (result.value) {
				window.location = 'index.php?page=data-pelanggan';
			}
		})</script>";
		}else{
		echo "<script>
		Swal.fire({title: 'Hapus Data Gagal',text: '',icon: 'error',confirmButtonText: 'OK'
		}).then((result) => {
			if (result.value) {
				window.location = 'index.php?page=data-pelanggan';
			}
		})</script>";
	}
}
