<?php
// ==================== SAVE HANDLER ====================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_titipan'])) {
	if (empty($_POST['id_pelanggan'])) {
		$_SESSION['error_message'] = 'ID Pelanggan kosong!';
		header("Location: ?page=titipan");
		exit;
	}
	$id_pelanggan = $koneksi->real_escape_string($_POST['id_pelanggan']);
	$jumlah = !empty($_POST['jumlah']) ? intval($_POST['jumlah']) : 'NULL';
	$keterangan = $koneksi->real_escape_string($_POST['keterangan'] ?? '');
	$kode_unik = intval(preg_replace('/[^0-9]/', '', $id_pelanggan));
	$query = "INSERT INTO tb_titipan (id_pelanggan, bulan, tahun, jumlah, kode_unik, status, tgl_titipan, keterangan) VALUES ('$id_pelanggan', '".date('m')."', '".date('Y')."', $jumlah, $kode_unik, 'BL', '".date('Y-m-d')."', '$keterangan')";
	if ($koneksi->query($query)) {
		$_SESSION['success_message'] = 'Data Titipan Berhasil Disimpan';
	} else {
		$_SESSION['error_message'] = 'Gagal menyimpan data: ' . $koneksi->error;
	}
	header("Location: ?page=titipan");
	exit;
}
