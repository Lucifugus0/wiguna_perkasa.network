<?php
	$sql = $koneksi->query("SELECT count(id_tagihan) as tagih_b from tb_tagihan where status='BL' and id_pelanggan='$data_id'");
	while ($data= $sql->fetch_assoc()) {
	
		$tagih=$data['tagih_b'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT count(id_tagihan) as tagih_l from tb_tagihan where status='LS' and id_pelanggan='$data_id'");
	while ($data= $sql->fetch_assoc()) {
	
		$lunas=$data['tagih_l'];
	}
	
	$cek_pelanggan = $koneksi->query("SELECT * FROM tb_pelanggan WHERE id_pelanggan = '$data_id'");
	$data_pelanggan = $cek_pelanggan->fetch_assoc();
	$cek_paket = $koneksi->query("SELECT * FROM tb_paket WHERE id_paket = '{$data_pelanggan['id_paket']}'");
	$data_paket = $cek_paket->fetch_assoc();
?>



<section class="content-header">
	<h1>
		Dashboard |
		<small>Pelanggan</small>
	</h1>
</section>

<!-- Main content -->
<section class="content">
	<!-- Small boxes (Stat box) -->
	<div class="row">

		<div class="col-lg-6 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-yellow">
				<div class="inner">
					<h2>
						<b>
							<?= $tagih; ?>
						</b>
					</h2>

					<p>Belum Bayar</p>
				</div>
				<div class="icon">
					<i class="ion-sad"></i>
				</div>
				<a href="?page=data-tagihan&status=BL" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>

		<div class="col-lg-6 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-green">
				<div class="inner">
					<h2>
						<b>
							<?= $lunas; ?>
						</b>
					</h2>

					<p>Lunas</p>
				</div>
				<div class="icon">
					<i class="ion-happy"></i>
				</div>
				<a href="?page=data-tagihan&status=LS" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
	</div>

	<div class="box box-primary">
		<div class="box-header with-border">
			DATA PELANGGAN
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
				<table id="" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>E-Mail</th>
							<th>Password</th>
							<th>No HP</th>
							<th>id_paket</th>
						</tr>
					</thead>
					<tbody>

						<?php
                  $no = 1;
                  $sql = $koneksi->query("SELECT * from tb_pelanggan where id_pelanggan='$data_id'");
                  while ($data= $sql->fetch_assoc()) {
                ?>

						<tr>
							<td>
								<?php echo $data['email']; ?>
							</td>

							<td>
								<?php echo $data['password']; ?>
							</td>

							<td>
								<?php echo $data['no_hp']; ?>
							</td>

							<td>
								<?php echo $data['id_paket']; ?>
							</td>
						</tr>
						<?php
						}
						?>
					</tbody>
			</div>
		</div>
	</div>
</section>

				<table class="table">
					<caption style="font-size: 16px; font-weight: bold; margin-bottom:5px;">Pembayaran Wifi Dapat Dilakukan sebagai berikut:</caption>
					<tr>
						<td>
							<span style="display: block;"> <b>->> BUKA APLIKASI m-banking<b> / e-wallet yang mendukung QRIS (Dana, OVO, GoPay, BCA, Mandiri, BRI, dll).</span>
							<div style="margin-top: 10px;">
								<img src="https://wigunaperkasa.my.id/qris-mandiri.jpeg" style="width: 200px; border-radius: 10px;" alt="QRIS Mandiri" />
							</div>
						</td>
					</tr>
				</table>
				<b>#PENTING</b><br />
				<b>Untuk pembayaran melalui QR manual<br />
				Jumlah Tagihan adalah<b> Rp<?= number_format($data_paket['tarif']+str_replace("C", "", $data_pelanggan['id_pelanggan']), 0, ',', '.') ?><br /><br />
				Screenshot bukti pembayaran, kirim ke WA 082223222210<br />
				Info lebih lanjut hubungi Wiguna Perkasa Network.
			</div>
		</div>
	</div>
</section>
