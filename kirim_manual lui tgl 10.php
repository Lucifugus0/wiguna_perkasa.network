<?php
include_once "inc/koneksi.php";
include_once "inc/rupiah.php";

$id_pelanggan = @$_GET['id_pelanggan'];
$id_tagihan = @$_GET['id_tagihan'];

$check_pelanggan = $koneksi->query("SELECT * FROM tb_pelanggan WHERE id_pelanggan = '$id_pelanggan'");
if (mysqli_num_rows($check_pelanggan) == 0)
	die("Gagal: Pelanggan tidak ditemukan!");
$data_pelanggan = $check_pelanggan->fetch_assoc();

$check_tagihan = $koneksi->query("SELECT * FROM tb_tagihan WHERE id_tagihan = '$id_tagihan' AND status = 'BL'");
if (mysqli_num_rows($check_tagihan) == 0)
	die("Gagal: Tagihan tidak ditemukan!");
$data_tagihan = $check_tagihan->fetch_assoc();

$bulan = date("m");
$tahun = date("Y");
$tagihan_nama = $data_pelanggan['nama'];
$tagihan_rupiah = explode(',', rupiah($data_tagihan['tagihan'] + $data_tagihan['kode_unik']))[0];

$wa_message = "*Halo,*
*Pelanggan Setia Wiguna Perkasa Network*

Kak, izin ngingetin yaaa 🙏
Tagihan WiFi bulan ini belum keliatan nih…☺️☺️☺️
Biar koneksi aktif kembali, boleh banget dilunasin dulu 🙏🙏

*Informasi Akun*
Sdr/i 			: *$tagihan_nama,*
Nominal Tagihan : *$tagihan_rupiah.* 
*untuk Bulan $bulan Tahun $tahun.*
*Batas akhir pembayaran sampai tanggal 10 ya kak*..☺️☺️

Kak, buat yang sibuk, sekarang bisa bayar WiFi langsung lewat QRIS ya, *tinggal scan aja QRIS di atas!* ✨

*PENTING* :
*1.Pembayaran diluar KODE QR yang terdaftar diatas bukan tanggung jawab kami.*
*2.Jika sudah melakukan pembayaran dan tidak sesuai nominal yang ditentukan, harap konfirmasi.*

Thanks banget udah jadi pelanggan setia 💙

✅ *Layanan 24jam* :
082 2232 22210 Wiguna Perkasa.";

$wa_target = $data_pelanggan['no_hp'];
$wa_target_cadangan = $data_pelanggan['no_hp_cadangan'];

$image_url = "http://localhost/qris-mandiri.jpg";
$ch_qris = curl_init();
curl_setopt_array($ch_qris, [
	CURLOPT_URL => "https://qrisku.my.id/api",
	CURLOPT_POST => TRUE,
	CURLOPT_POSTFIELDS => json_encode([
		'amount' => ($data_tagihan['tagihan'] + $data_tagihan['kode_unik']),
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

$query_builder = http_build_query([
	'number' => '6287885536663',
	'target' => $wa_target,
	'image_url' => $image_url,
	'message' => $wa_message
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

if (!empty($wa_target_cadangan)) {
	$query_builder = http_build_query([
		'number' => '6287885536663',
		'target' => $wa_target_cadangan,
		'image_url' => $image_url,
		'message' => $wa_message
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
}

unlink("temp.jpg");

echo "Berhasil: Pesan berhasil dikirim!";
