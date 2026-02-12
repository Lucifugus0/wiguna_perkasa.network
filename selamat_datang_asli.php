<?php
include_once "inc/koneksi.php";
include_once "inc/rupiah.php";

$no_hp = trim(@$_GET['no_hp'] ?? '');
$no_hp_cadangan = trim(@$_GET['no_hp_cadangan'] ?? '');

$cek_pelanggan = $koneksi->query("SELECT * FROM tb_pelanggan WHERE no_hp = '$no_hp'");
$pelanggan = $cek_pelanggan->fetch_assoc();
$id_paket = $pelanggan['id_paket'];
$cek_paket = $koneksi->query("SELECT * FROM tb_paket WHERE id_paket = '$id_paket'");
$paket = $cek_paket->fetch_assoc();

$tagihan_nama = $pelanggan['nama'];
$kode_unik = preg_replace('/[^0-9]/', '', $pelanggan['id_pelanggan']);
$tagihan = $paket['tarif'] + $kode_unik;
$tagihan_rupiah = explode(',', rupiah($tagihan))[0];

$image_url = "http://localhost/qris-mandiri.jpg";
$ch_qris = curl_init();
curl_setopt_array($ch_qris, [
	CURLOPT_URL => "https://qrisku.my.id/api",
	CURLOPT_POST => TRUE,
	CURLOPT_POSTFIELDS => json_encode([
		'amount' => $tagihan,
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
	file_put_contents("temp.jpg", $image_data);
	$image_url = "http://localhost/wigunaperkasa.id/temp.jpg";
}

$kata_selamat_datang = 
"Selamat Anda sudah terdaftar di member
*AKTIF  Wiguna Perkasa Network.*
Gunakan internet sebaik mungkin, jangan lupa istirahat dan waktu buat keluarga ☺️☺️☺️

*Informasi Akun*
Sdr/i 			: *$tagihan_nama,*
Nominal Tagihan : *$tagihan_rupiah.*
Pembayaran dilakukan setiap bulan, Dari mulai tanggal 1 sampai tanggal 5.

✅ *Layanan 24jam*
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
