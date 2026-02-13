<?php
// ==================== DELETE HANDLER ====================
if (isset($_GET['hapus']) && isset($_GET['token'])) {
	if (isset($_SESSION['delete_token']) && $_GET['token'] === $_SESSION['delete_token']) {
		$id_titipan = intval($_GET['hapus']);
		if ($koneksi->query("DELETE FROM tb_titipan WHERE id_titipan = $id_titipan")) {
			$_SESSION['success_message'] = 'Data Titipan Berhasil Dihapus';
		} else {
			$_SESSION['error_message'] = 'Gagal menghapus data: ' . $koneksi->error;
		}
		unset($_SESSION['delete_token']);
	} else {
		$_SESSION['error_message'] = 'Token tidak valid atau sudah expired';
	}
	header("Location: ?page=titipan");
	exit;
}
