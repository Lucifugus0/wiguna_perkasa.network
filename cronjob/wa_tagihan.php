<?php
date_default_timezone_set("Asia/Jakarta");

include "../inc/koneksi.php";
include "../inc/rupiah.php";

$hari_ini = date("d");
$bulan = date("m");
$tahun = date("Y");

$cek_tagihan = $koneksi->query("SELECT p.id_pelanggan, p.nama, p.mac_modem, p.alamat, p.patokan, p.no_hp, t.id_tagihan, t.tagihan, t.kode_unik, t.status, t.tgl_bayar
from tb_pelanggan p inner join tb_tagihan t on p.id_pelanggan=t.id_pelanggan where p.nama LIKE '%$hari_ini%' and t.bulan = '$bulan' and t.tahun = '$tahun' and t.status='BL'
order by status asc");

while ($data_tagihan = $cek_tagihan->fetch_assoc()) {
	$tagihan_nama = $data_tagihan['nama'];
	$tagihan_rupiah = explode(',', rupiah($data_tagihan['tagihan'] + $data_tagihan['kode_unik']))[0];
	
	$wa_message = "*Pelanggan Yth,*
*Tagihan WiFi Wiguna perkasa Network*

Sdr/i *$tagihan_nama.*
Sudah bisa dibayarkan dari sekarang, pembayaran Tagihan Internet sebesar *$tagihan_rupiah.* 
Mohon segera melakukan pembayaran dengan nominal yang tertera di atas *termasuk KODE UNIK*. untuk Bulan $bulan Tahun $tahun.

sekarang bisa bayar WiFi langsung lewat QRIS ya, tinggal scan aja QRIS di atas! ✨

Informasi tata cara pembayaran:
*Scan kode QR diatas lalu masukan jumlah nominal dan KODE UNIK.*
Silahkan Ketik secara manual dan buka di Browser, Google Chrom dan Google :
http://allures.my.id/carapembayaran/bayarwifi.txt

*PENTING* :
*1.Pembayaran diluar KODE QR yang terdaftar diatas bukan tanggung jawab kami.*
*2.Untuk pembayaran dan semua transaksi harus melalui KODE QR diatas dan sesuai nominal yang terdaftar termasuk kode unik, Agar bisa terinput secara otomatis di sistem.*
*3.Jika sudah melakukan pembayaran dan tidak sesuai nominal yang ditentukan, harap konfirmasi.*

Info lebih lanjut hubungi:
085222210036 Chandra

Abaikan informasi ini jika sudah melakukan pembayaran.
Terima kasih.

✅ *Layanan 24jam* :
082 2232 22210 Wiguna Perkasa.";
	
	$wa_target = $data_tagihan['no_hp'];
	$wa_target_cadangan = $data_tagihan['no_hp_cadangan'];
	
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
		$image_url = "http://allures.my.id/wigunaperkasa.id/cronjob/temp.jpg";
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
	
	unlink('temp.jpg');
}
