<?php
include "inc/koneksi.php";

file_put_contents("shopee.txt", "Step 1 berhasil!");
if ($_GET) {
	file_put_contents("shopee.txt", "Step 2 berhasil!");
	$post_data = $_GET;
	$pkg = @$post_data['pkg'];
	$text = @$post_data['text'];
	
	if ($pkg != 'com.shopee.id') {
		file_put_contents("shopee.txt", "Step 3 gagal!");
		die("Invalid request!");
	} else {
		file_put_contents("shopee.txt", "Step 3 berhasil!");
		$filter_jumlah = trim(strtolower($text));
		$filter_jumlah = trim(explode('rp', $filter_jumlah)[1] ?? '');
		$filter_jumlah = trim(str_replace('.', '', $filter_jumlah));
		
		if (!$filter_jumlah) {
			file_put_contents("shopee.txt", "Step 4 gagal!");
			die("Invalid request!");
		}
		
		file_put_contents("shopee.txt", "Step 4 berhasil!");
		$filter_jumlah = explode(' ', $filter_jumlah)[0];
		$jumlah_dibayar = $filter_jumlah;
		
		$cek_tagihan = $koneksi->query("SELECT * FROM tb_tagihan WHERE tagihan + kode_unik = $jumlah_dibayar AND status = 'BL'");
		
		$data_tagihan = $cek_tagihan->fetch_assoc();
		$tgl_bayar = date("Y-m-d");
		$id_tagihan = $data_tagihan['id_tagihan'];
		$koneksi->query("UPDATE tb_tagihan SET status = 'LS', tgl_bayar = '$tgl_bayar' AND kode_unik != 0 WHERE id_tagihan = '$id_tagihan'");
		file_put_contents("shopee.txt", "Step 5 berhasil!");

		// Cek juga di tb_titipan
		$cek_titipan = $koneksi->query("SELECT * FROM tb_titipan WHERE jumlah + kode_unik = $jumlah_dibayar AND kode_unik != 0 AND status = 'BL'");
		if ($cek_titipan && $cek_titipan->num_rows > 0) {
			$data_titipan = $cek_titipan->fetch_assoc();
			$koneksi->query("UPDATE tb_titipan SET status = 'LS' WHERE id_titipan = '" . $data_titipan['id_titipan'] . "'");
			file_put_contents("shopee.txt", "Step 6 titipan berhasil!");
		}
	}
} else {
	file_put_contents("shopee.txt", "Step 2 gagal!");
}
