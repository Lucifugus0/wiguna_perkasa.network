<script>
jQuery(document).ready(function($) {
	console.log('Titipan script loaded!');
	console.log('jQuery version:', $.fn.jquery);
	console.log('Select2 available:', typeof $.fn.select2);

	// Alert message
	<?php if (!empty($alert_message)) { ?>
	Swal.fire({
		title: '<?= addslashes($alert_message) ?>',
		icon: '<?= $alert_type ?>',
		confirmButtonText: 'OKE'
	});
	<?php } ?>

	// Destroy & reinit DataTable
	if ($.fn.DataTable.isDataTable('#example1')) {
		$('#example1').DataTable().destroy();
	}
	$("#example1").DataTable({
		paging: true,
		lengthChange: true,
		searching: true,
		ordering: true,
		language: {
			lengthMenu: "Tampilkan _MENU_ data per halaman",
			zeroRecords: "Data tidak ditemukan",
			info: "Menampilkan halaman _PAGE_ dari _PAGES_",
			search: "Cari:",
			paginate: { next: "Selanjutnya", previous: "Sebelumnya" }
		}
	});

	// Initialize Select2 dengan event listener langsung
	$('.select2-titipan').select2({
		placeholder: "-- Pilih Pelanggan --",
		allowClear: true
	}).on('select2:select', function(e) {
		var $opt = $(this).find(':selected');
		var id_pelanggan = $opt.val();

		// Set hidden input untuk form submit
		$('#hidden_id_pelanggan').val(id_pelanggan);

		// Set display fields
		$('#txt_id_pelanggan').val(id_pelanggan);
		$('#txt_nama').val($opt.data('nama'));
		$('#txt_alamat').val($opt.data('alamat'));
		$('#txt_patokan').val($opt.data('patokan') || '-');
		$('#txt_paket').val($opt.data('paket') || '-');
		var tarif = $opt.data('tarif') || 0;
		$('#txt_tarif').val(tarif ? 'Rp ' + tarif.toLocaleString('id-ID') : '-');
		$('#harga_paket_value').val(tarif);
		$('#jumlah_titipan').val('').attr('max', tarif);
		$('textarea[name="keterangan"]').val('');
	}).on('select2:clear', function() {
		// Clear hidden input
		$('#hidden_id_pelanggan').val('');

		// Clear display fields
		$('#txt_id_pelanggan, #txt_nama, #txt_alamat, #txt_patokan, #txt_paket, #txt_tarif').val('');
		$('#harga_paket_value').val(0);
		$('#jumlah_titipan').val('').removeAttr('max');
		$('textarea[name="keterangan"]').val('');
	});

	// Form submit handler
	$('#formTitipan').on('submit', function() {
		var $btn = $(this).find('button[type="submit"]');
		$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
	});
});

function showQR(id) {
	var $modal = jQuery('#qrModal');
	var $body = jQuery('#qrModalBody');
	var $btnDownload = jQuery('#btnDownloadQR');

	// Show modal & reset
	$modal.modal('show');
	$btnDownload.hide();
	$body.html('<div class="text-center" style="padding: 40px 20px;"><i class="fa fa-spinner fa-spin fa-3x text-primary"></i><p style="margin-top: 15px; font-size: 16px;">Generating QR Code...</p></div>');

	// Fetch QR data
	jQuery.get('?page=titipan&ajax_qr=' + id, function(res) {
		if (res.success) {
			var qrBase64 = res.qr_base64;
			var html = `
				<div class="row">
					<div class="col-md-6 text-center">
						<img src="data:image/png;base64,${qrBase64}" id="qrImage" style="max-width: 100%; border: 2px solid #ddd; padding: 10px; border-radius: 5px; background: white;">
					</div>
					<div class="col-md-6">
						<h4 style="margin-top: 0; color: #605ca8;"><i class="fa fa-user"></i> Informasi Pelanggan</h4>
						<table class="table table-bordered">
							<tr>
								<th width="40%">ID Pelanggan</th>
								<td><strong>${res.id_pelanggan}</strong></td>
							</tr>
							<tr>
								<th>Nama</th>
								<td>${res.nama}</td>
							</tr>
							<tr>
								<th>Alamat</th>
								<td>${res.alamat}</td>
							</tr>
							<tr>
								<th>Patokan</th>
								<td>${res.patokan}</td>
							</tr>
							<tr>
								<th>Paket</th>
								<td>${res.paket}</td>
							</tr>
						</table>
						<h4 style="color: #605ca8;"><i class="fa fa-money"></i> Detail Pembayaran</h4>
						<table class="table table-bordered">
							<tr>
								<th width="40%">Harga Paket</th>
								<td>Rp ${res.harga_paket}</td>
							</tr>
							<tr>
								<th>Jumlah Titipan</th>
								<td>Rp ${res.jumlah_titipan}</td>
							</tr>
							<tr>
								<th>Kode Unik</th>
								<td>Rp ${res.kode_unik}</td>
							</tr>
							<tr style="background: #f9f9f9; font-weight: bold;">
								<th>TOTAL BAYAR</th>
								<td style="color: #d9534f; font-size: 18px;">Rp ${res.total_bayar}</td>
							</tr>
						</table>
						${res.keterangan && res.keterangan !== '-' ? '<p><strong>Keterangan:</strong> ' + res.keterangan + '</p>' : ''}
						<p class="text-muted"><small><i class="fa fa-calendar"></i> Tanggal: ${res.tanggal}</small></p>
					</div>
				</div>
			`;
			$body.html(html);

			// Show download button & attach handler
			$btnDownload.show().off('click').on('click', function() {
				downloadQR(qrBase64, res.id_pelanggan, res.nama);
			});
		} else {
			$body.html('<div class="alert alert-danger"><i class="fa fa-warning"></i> ' + (res.message || 'Gagal memuat QR Code') + '</div>');
		}
	}).fail(function() {
		$body.html('<div class="alert alert-danger"><i class="fa fa-warning"></i> Error: Gagal terhubung ke server</div>');
	});
}

function downloadQR(base64, idPelanggan, nama) {
	// Convert base64 to blob
	var byteString = atob(base64);
	var ab = new ArrayBuffer(byteString.length);
	var ia = new Uint8Array(ab);
	for (var i = 0; i < byteString.length; i++) {
		ia[i] = byteString.charCodeAt(i);
	}
	var blob = new Blob([ab], {type: 'image/png'});

	// Create download link
	var url = window.URL.createObjectURL(blob);
	var a = document.createElement('a');
	a.href = url;
	a.download = 'QR_Titipan_' + idPelanggan + '_' + nama.replace(/\s+/g, '_') + '.png';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	window.URL.revokeObjectURL(url);

	Swal.fire({
		title: 'Download Berhasil!',
		text: 'QR Code telah didownload',
		icon: 'success',
		timer: 2000
	});
}
</script>
