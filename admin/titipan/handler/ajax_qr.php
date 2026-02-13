<?php
// ==================== AJAX QR HANDLER ====================
if (isset($_GET['ajax_qr'])) {
	header('Content-Type: application/json');
	$id_titipan = intval($_GET['ajax_qr']);
	if ($id_titipan <= 0) {
		echo json_encode(['success' => false, 'message' => 'ID Titipan tidak valid']);
		exit;
	}
	$qr_data = $koneksi->query("SELECT t.*, p.nama, p.alamat, p.patokan, pk.paket, pk.tarif FROM tb_titipan t LEFT JOIN tb_pelanggan p ON t.id_pelanggan = p.id_pelanggan LEFT JOIN tb_paket pk ON p.id_paket = pk.id_paket WHERE t.id_titipan = $id_titipan AND t.status = 'BL'")->fetch_assoc();
	if ($qr_data) {
		$harga_paket = $qr_data['tarif'] ?? 0;
		$jumlah_titipan = $qr_data['jumlah'] ?? 0;
		$total_bayar = $harga_paket + $qr_data['kode_unik'];
		$ch_qris = curl_init();
		curl_setopt_array($ch_qris, [CURLOPT_URL => "https://qrisku.my.id/api", CURLOPT_POST => TRUE, CURLOPT_POSTFIELDS => json_encode(['amount' => $total_bayar, 'qris_statis' => '00020101021126690021ID.CO.BANKMANDIRI.WWW01189360000801821981980211718219819800303UMI51440014ID.CO.QRIS.WWW0215ID10254094363220303UMI5204274153033605802ID5922Wiguna Perkasa Network6013Cirebon (Kab)61054516262070703A016304FA5D']), CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_FOLLOWLOCATION => TRUE]);
		$ch_qris_response = json_decode(curl_exec($ch_qris));
		curl_close($ch_qris);
		if ($ch_qris_response && isset($ch_qris_response->qris_base64)) {
			echo json_encode(['success' => true, 'qr_base64' => $ch_qris_response->qris_base64, 'id_pelanggan' => $qr_data['id_pelanggan'], 'nama' => $qr_data['nama'], 'alamat' => $qr_data['alamat'], 'patokan' => $qr_data['patokan'] ?? '-', 'paket' => $qr_data['paket'] ?? '-', 'harga_paket' => number_format($harga_paket, 0, ',', '.'), 'jumlah_titipan' => $jumlah_titipan ? number_format($jumlah_titipan, 0, ',', '.') : '-', 'kode_unik' => number_format($qr_data['kode_unik'], 0, ',', '.'), 'total_bayar' => number_format($total_bayar, 0, ',', '.'), 'keterangan' => $qr_data['keterangan'] ?? '-', 'tanggal' => $qr_data['tgl_titipan']]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Gagal generate QR Code']);
		}
	} else {
		echo json_encode(['success' => false, 'message' => 'Data titipan tidak ditemukan']);
	}
	exit;
}
