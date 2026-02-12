<section class="content">
	<?php
	$bulan = @$_GET['bulan'] ?? date("m");
	$tahun = @$_GET['tahun'] ?? date("Y");
	?>
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">TAGIHAN LUNAS</h3>
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
			<form action="" class="row" method="GET">
				<input type="hidden" name="page" value="lunas-tagihan">
				<div class="col-md-4 form-group">
					<select class="form-control" name="bulan" id="bulan">
						<option value="">Pilih bulan</option>
						<?php
						$tb_bulan = $koneksi->query("SELECT * FROM tb_bulan");
						while ($data_bulan = $tb_bulan->fetch_assoc()) { ?>
							<option value="<?= $data_bulan['id_bulan'] ?>" <?= $bulan == $data_bulan['id_bulan'] ? 'selected' : '' ?>><?= $data_bulan['id_bulan'] ?> - <?= $data_bulan['bulan'] ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-md-4 form-group">
					<select class="form-control" name="tahun" id="tahun">
						<option value="">Pilih tahun</option>
						<?php for ($i = 2022; $i <= date("Y"); $i++) { ?>
						<option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-md-4 form-group">
					<button type="submit" class="btn btn-primary" style="display: block;">Filter</button>
				</div>
			</form>
			<div class="table-responsive">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th>Pelanggan</th>
							<th>Alamat</th>
							<th>Patokan</th>
							<th>Bulan/Tahun</th>
							<th>Tagihan</th>
							<th>Status</th>
							<th>Tanggal</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>

						<?php
				  if (!empty($bulan) && !empty($tahun)) {
					  $no = 1;
					  $sql = $koneksi->query("SELECT p.id_pelanggan, p.nama, p.alamat, p.patokan, p.no_hp, t.id_tagihan, t.tagihan, t.status, t.tgl_bayar, t.bulan, t.tahun   
					  from tb_pelanggan p inner join tb_tagihan t on p.id_pelanggan=t.id_pelanggan where status='LS' and bulan = '$bulan' and tahun = '$tahun'
					  order by tgl_bayar desc");
					  while ($data= $sql->fetch_assoc()) {
				  ?>

						<tr>
							<td>
								<?php echo $no++; ?>
							</td>
							<td>
								<?php echo $data['id_pelanggan']; ?>
								-
								<?php echo $data['nama']; ?>
							</td>
							<td>
								<?php echo $data['alamat']; ?>
							</td>
							<td>
								<?php echo $data['patokan']; ?>
							</td>
							<td>
								<?php echo $data['bulan']; ?>
								/
								<?php echo $data['tahun']; ?>
							</td>
							<td>
								<?php echo rupiah($data['tagihan']); ?>
							</td>
							<td>
								<?php $stt = $data['status']  ?>
								<?php if($stt == 'BL'){ ?>
								<span class="label label-danger">Belum Bayar</span>
								<?php }elseif($stt == 'LS'){ ?>
								<span class="label label-success">LUNAS</span>
							</td>
							<td>
								<?php  $tgl = $data['tgl_bayar']; echo date("d-M-Y", strtotime($tgl))?>
							</td>
							<?php } ?>

							<td>
								<a href="./report/cetak_struk.php?id_tagihan=<?php echo $data['id_tagihan']; ?>"
								 target=" _blank" title="Cetak Struk" class="btn btn-primary">
									<i class="glyphicon glyphicon-print"></i> Struk</a>
							</td>
						</tr>
					<?php
						}
					}
					?>
					</tbody>

				</table>
				<!-- /.box-body -->
			</div>
		</div>
	</div>
</section>