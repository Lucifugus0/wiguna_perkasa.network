<section class="content">
	<div class="box box-primary">
		<div class="box-header with-border">
			<a href="?page=add-pelanggan" title="Tambah Data" class="btn btn-primary">
				<i class="glyphicon glyphicon-plus"></i> DATA PELANGGAN</a>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse">
					<i class="fa fa-minus"></i>
				</button>
				<button type="button" class="btn btn-box-tool" data-widget="remove">
					<i class="fa fa-remove"></i>
				</button>

			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="table-responsive">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th>ID</th>
							<th>Nama</th>
							<th>Mac / SN Modem</th>
							<th>IP Modem</th>
							<th>Teknisi</th>
							<th>Titik ODP</th>
							<th>Alamat</th>
							<th>Patokan</th>
							<th>No HP Utama</th>
							<th>No HP Cadangan</th>
							<th>E-Mail</th>
							<th>Password</th>
							<th>Paket</th>
							<th>HP Otomatis</th>
							<th>Tanggal Pemasangan</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$no = 1;
							$sql = $koneksi->query("SELECT * from tb_pelanggan");
							while ($data= $sql->fetch_assoc()) {
						?>
						<tr>
							<td>
								<?php echo $no++; ?>
							</td>
							<td>
								<?php echo $data['id_pelanggan']; ?>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="nama" class="editable"><?= $data['nama'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'nama');" data-btn="nama-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="mac_modem" class="editable"><?= $data['mac_modem'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'mac_modem');" data-btn="mac_modem-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="ip_modem" class="editable"><?= $data['ip_modem'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'ip_modem');" data-btn="ip_modem-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="teknisi" class="editable"><?= $data['teknisi'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'teknisi');" data-btn="teknisi-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="titik_odp" class="editable"><?= $data['titik_odp'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'titik_odp');" data-btn="titik_odp-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="alamat" class="editable"><?= $data['alamat'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'alamat');" data-btn="alamat-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="patokan" class="editable"><?= $data['patokan'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'patokan');" data-btn="patokan-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="no_hp" class="editable"><?= $data['no_hp'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'no_hp');" data-btn="no_hp-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="no_hp_cadangan" class="editable"><?= $data['no_hp_cadangan'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'no_hp_cadangan');" data-btn="no_hp_cadangan-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="email" class="editable"><?= $data['email'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'email');" data-btn="email-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="password" class="editable"><?= $data['password'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'password');" data-btn="password-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<?php echo $data['id_paket']; ?>
							</td>
							<td>
								<span data-id="<?= $data['id_pelanggan'] ?>" data-field="hp_otomatis" class="editable"><?= $data['hp_otomatis'] ?></span>
								<a href="javascript:editField('<?= $data['id_pelanggan'] ?>', 'hp_otomatis');" data-btn="hp_otomatis-<?= $data['id_pelanggan'] ?>">
									<i class="fa fa-pencil-square-o text-warning" style="margin-left: 5px;"></i>
								</a>
							</td>
							<td>
								<?php echo $data['tanggal_pemasangan']; ?>
							</td>
							<td>
								<a target="_blank" href="selamat_datang.php?no_hp=<?php echo $data['no_hp']; ?>&no_hp_cadangan=<?php echo $data['no_hp_cadangan']; ?>" title="Kirim Whatsapp Selamat Datang"
								 class="btn btn-success">
									<i class="fa fa-whatsapp"></i>
								</a>
								<a target="_blank" href="pelanggan_minta_qr.php?no_hp=<?php echo $data['no_hp']; ?>&no_hp_cadangan=<?php echo $data['no_hp_cadangan']; ?>" title="Kirim Whatsapp Selamat Datang"
								 class="btn btn-primary">
									<i class="fa fa-qrcode"></i>
								</a>
								<?php if ($data_user == 'Wiguna') { ?>
								<a href="?page=edit-pelanggan&kode=<?php echo $data['id_pelanggan']; ?>" title="Ubah"
								 class="btn btn-warning">
									<i class="glyphicon glyphicon-edit"></i>
								</a>
								<a href="?page=del-pelanggan&kode=<?php echo $data['id_pelanggan']; ?>" onclick="return confirm('Yakin Hapus Data Ini ?')"
								 title="Hapus" class="btn btn-danger">
									<i class="glyphicon glyphicon-trash"></i>
								</a>
								<?php } ?>
							</td>
						</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

<script>
	function editField(id, field) {
		var span = $(`span[data-id="${id}"][data-field="${field}"]`);
		var currentValue = span.html().trim();
		var btn = $(`a[data-btn="${field}-${id}"]`);

		if (currentValue.startsWith('<div')) {
			var value = $(`input[data-edit="${field}-${id}"]`).val();
			span.html(value);
			btn.html(`<i class="fa fa-pencil-square-o text-warning"></i>`);
			return;
		}

		span.html(`
			<div class="input-group">
				<input type="text" class="form-control"
					style="width: ${currentValue.length < 5 ? 50 : currentValue.length*10}px;" 
					value="${currentValue}"
					data-edit="${field}-${id}">
				<button class="btn btn-warning btn-sm" 
						onclick="saveField('${id}', '${field}')">Simpan</button>
			</div>
		`);

		btn.html(`<i class="fa fa-close text-danger"></i>`);
	}

	function saveField(id, field) {
		var span = $(`span[data-id="${id}"][data-field="${field}"]`);
		var value = $(`input[data-edit="${field}-${id}"]`).val();
		var btn = $(`a[data-btn="${field}-${id}"]`);

		$.ajax({
			url: "ajax/pelanggan/edit_pelanggan.php",
			method: "POST",
			data: {
				id_pelanggan: id,
				field: field,
				value: value
			},
			success: function () {
				span.html(value);
				btn.html(`<i class="fa fa-pencil text-warning"></i>`);

				if (field == "no_hp" || "no_hp_cadangan") {
					window.location.reload();
				}
			},
			error: function () {
				alert("Gagal menyimpan perubahan!");
			}
		});
	}
</script>