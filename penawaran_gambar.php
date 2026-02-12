<?php
include_once "inc/koneksi.php";
include_once "inc/rupiah.php";

$no_hp = trim(@$_GET['no_hp'] ?? '');
$no_hp_cadangan = trim(@$_GET['no_hp_cadangan'] ?? '');

$cek_pelanggan = $koneksi->query("SELECT * FROM tb_pelanggan_off WHERE no_hp = '$no_hp' and no_hp_cadangan = '$no_hp_cadangan'");
$pelanggan = $cek_pelanggan->fetch_assoc();
$id_paket = $pelanggan['id_paket'];
$cek_paket = $koneksi->query("SELECT * FROM tb_paket WHERE id_paket = '$id_paket'");
$paket = $cek_paket->fetch_assoc();

$tagihan_nama = $pelanggan['nama'];

$image_url = "http://localhost/brosur-penawaran.jpeg";

$kata_selamat_datang = 
"Selamat Anda sudah terdaftar di member
*AKTIF  Wiguna Perkasa Network.*
Gunakan internet sebaik mungkin, jangan lupa istirahat dan waktu buat keluarga ☺️☺️☺️

*Informasi Akun*
Sdr/i 			: *$tagihan_nama,*
Pembayaran dilakukan setiap bulan, *Dari mulai tanggal 1 sampai batas akhir tanggal 5.*

*Layanan 24jam*
082 2232 22210 Wiguna Perkasa
Save nomor kami yaa ☺️☺️☺️";

$query_builder = http_build_query([
	'number' => '6287885536663',
	'target' => $no_hp,
	'image_url' => $image_url,
	'message' => $kata_selamat_datang
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:3333/api/chat/send-image-message?$query_builder");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	"Authorization: Bearer a7f5e56c-5235-4b03-8548-6b4000cf2731",
	"Content-Type: application/json",
]);
$response = curl_exec($ch);
curl_close($ch);

echo "No HP Utama : <br />" . $response;

if (!empty($no_hp_cadangan)) {
	$query_builder = http_build_query([
		'number' => '6287885536663',
		'target' => $no_hp_cadangan,
		'image_url' => $image_url,
		'message' => $kata_selamat_datang
	]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://localhost:3333/api/chat/send-image-message?$query_builder");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"Authorization: Bearer a7f5e56c-5235-4b03-8548-6b4000cf2731",
		"Content-Type: application/json",
	]);
	$response = curl_exec($ch);
	curl_close($ch);
	
	echo "<br />No HP Cadangan : <br />" . $response;
}
