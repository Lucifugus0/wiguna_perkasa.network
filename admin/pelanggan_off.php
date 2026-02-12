<section class="content">
	<div class="box box-primary">
		<div class="box-header with-border">
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
							<th>Nama</th>
							<th>Mac Modem</th>
							<th>Teknisi</th>
							<th>Titik ODP</th>
							<th>Alamat</th>
							<th>Patokan</th>
							<th>No HP Utama</th>
							<th>No HP Cadangan</th>
							<th>E-Mail</th>
							<th>Password</th>
							<th>Paket</th>
							<th>Tanggal Pemasangan</th>
							<th>Tanggal Off</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>

						<?php
                  $no = 1;
                  $sql = $koneksi->query("SELECT * from tb_pelanggan_off");
                  while ($data= $sql->fetch_assoc()) {
				      $recover_pelanggan = "&nama=".urlencode($data['nama'])
					      . "&mac_modem=".urlencode($data['mac_modem'])
						  . "&teknisi=".urlencode($data['teknisi'])
						  . "&titik_odp=".urlencode($data['titik_odp'])
						  . "&alamat=".urlencode($data['alamat'])
						  . "&patokan=".urlencode($data['patokan'])
						  . "&no_hp=".urlencode($data['no_hp'])
						  . "&no_hp_cadangan=".urlencode($data['no_hp_cadangan'])
						  . "&email=".urlencode($data['email'])
						  . "&password=".urlencode($data['password'])
						  . "&id_paket=".urlencode($data['id_paket'])
						  . "&tanggal_pemasangan=".urlencode(explode(' ', $data['tanggal_pemasangan'])[0]);
                ?>

						<tr>
							<td>
								<?php echo $no++; ?>
							</td>
							<td>
								<?php echo $data['nama']; ?>
							</td>
							<td>
								<?php echo $data['mac_modem']; ?>
							</td>
							<td>
								<?php echo $data['teknisi']; ?>
							</td>
							<td>
								<?php echo $data['titik_odp']; ?>
							</td>
							<td>
								<?php echo $data['alamat']; ?>
							</td>
							<td>
								<?php echo $data['patokan']; ?>
							</td>
							<td>
								<?php echo $data['no_hp']; ?>
							</td>
							<td>
								<?php echo $data['no_hp_cadangan']; ?>
							</td>
							<td>
								<?php echo $data['email']; ?>
							</td>
							<td>
								<?php echo $data['password']; ?>
							</td>
							<td>
								<?php echo $data['id_paket']; ?>
							</td>
							<td>
								<?php echo $data['tanggal_pemasangan']; ?>
							</td>
							<td>
								<?php echo $data['tanggal_off']; ?>
							</td>
							<td>
								<a target="_blank" href="penawaran_gambar.php?no_hp=<?php echo $data['no_hp']; ?>&no_hp_cadangan=<?php echo $data['no_hp_cadangan']; ?>" title="Kirim Whatsapp Penawaran"
								 class="btn btn-success">
									<i class="fa fa-image"></i>
								</a>
								<a target="_blank" href="penawaran.php?no_hp=<?php echo $data['no_hp']; ?>&no_hp_cadangan=<?php echo $data['no_hp_cadangan']; ?>" title="Kirim Whatsapp Penawaran"
								 class="btn btn-success">
									<i class="fa fa-whatsapp"></i>
								</a>
								<a target="_blank" href="http://allures.my.id/wigunaperkasa.id/?page=add-pelanggan<?= $recover_pelanggan ?>" title="Kembalikan ke PSB"
								 class="btn btn-primary">
									<i class="fa fa-user"></i>
								</a>
								<a href="?page=edit-pelanggan-off&kode=<?php echo $data['id']; ?>" title="Ubah"
								 class="btn btn-warning">
									<i class="glyphicon glyphicon-edit"></i>
								</a>
								<a href="?page=del-pelanggan-off&kode=<?php echo $data['id']; ?>" title="Hapus"
								 class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus Pelanggan Off ini?');">
									<i class="glyphicon glyphicon-trash"></i>
								</a>
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