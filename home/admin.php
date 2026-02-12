<?php
	$sql = $koneksi->query("SELECT count(id_paket) as paket from tb_paket");
	while ($data= $sql->fetch_assoc()) {
	
		$paket=$data['paket'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT count(id_pelanggan) as huni from tb_pelanggan");
	while ($data= $sql->fetch_assoc()) {
	
		$huni=$data['huni'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT count(id_tagihan) as tagih_b from tb_tagihan where status='BL'");
	while ($data= $sql->fetch_assoc()) {
	
		$tagih=$data['tagih_b'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT count(id_tagihan) as tagih_l from tb_tagihan where status='LS'");
	while ($data= $sql->fetch_assoc()) {
	
		$lunas=$data['tagih_l'];
	}
?>

<?php
	$sql = $koneksi->query("SELECT SUM(tagihan) as total_tagihan from tb_tagihan where status='BL'");
	$data_tagih_total = $sql->fetch_assoc();
	$tagih_total = $data_tagih_total['total_tagihan'];
?>


<?php
	$sql = $koneksi->query("SELECT * FROM tb_pelanggan");
	$seluruh_tagihan_total = 0;
	while ($data_pelanggan = $sql->fetch_assoc()) {
		$check_paket = $koneksi->query("SELECT * FROM tb_paket WHERE id_paket = '{$data_pelanggan['id_paket']}'");
		$data_paket = $check_paket->fetch_assoc();
		$seluruh_tagihan_total = $seluruh_tagihan_total+$data_paket['tarif'];
	}
?>

<section class="content-header">
	<h1>
		Dashboard |
		<small>Administrator</small>
	</h1>
</section>

<!-- Main content -->
<section class="content">
	<!-- Small boxes (Stat box) -->
	<div class="row">

		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-primary">
				<div class="inner">
					<h2>
						<b>
							<?= $paket; ?>
						</b>
					</h2>

					<p>Paket</p>
				</div>
				<div class="icon">
					<i class="ion-ios-download"></i>
				</div>
				<a href="/wigunaperkasa.id/?page=data-paket" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>

		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-yellow">
				<div class="inner">
					<h2>
						<b>
							<?= $huni; ?>
						</b>
					</h2>

					<p>Pelanggan</p>
				</div>
				<div class="icon">
					<i class="ion-person-stalker"></i>
				</div>
				<a href="/wigunaperkasa.id/?page=data-pelanggan" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>

		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-red">
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
				<a href="/wigunaperkasa.id/?page=buka-tagihan" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>

		<div class="col-lg-3 col-xs-6">
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
				<a href="/wigunaperkasa.id/?page=lunas-tagihan" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
		
		<?php if ($data_user == 'Wiguna') { ?>
		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-red">
				<div class="inner">
					<h2>
						<b>
							Rp<?= number_format($tagih_total, 0, ',', '.'); ?>
						</b>
					</h2>

					<p>Total Belum Bayar</p>
				</div>
				<div class="icon">
					<i class="ion-sad"></i>
				</div>
				<a href="/wigunaperkasa.id/?page=buka-tagihan" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
		
		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-green">
				<div class="inner">
					<h2>
						<b>
							Rp<?= number_format($seluruh_tagihan_total, 0, ',', '.'); ?>
						</b>
					</h2>

					<p>Total Seluruh Tagihan</p>
				</div>
				<div class="icon">
					<i class="ion-happy"></i>
				</div>
				<a href="/wigunaperkasa.id/?page=data-pelanggan" class="small-box-footer">More info
					<i class="fa fa-arrow-circle-right"></i>
				</a>
			</div>
		</div>
		<?php } ?>