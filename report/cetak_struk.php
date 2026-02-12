<?php
include "../inc/koneksi.php";
include "../inc/rupiah.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Cetak Struk Kos</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../dist/img/print.jpg">
    <style>
		body {
			height: 100vh;
			width: 100vw;
			display: flex;
		}
	
        .printbody {
            font-family: Arial, sans-serif;
            font-size: 16px;
            margin: auto;
            border: 1px solid black;
			padding: 5px;
            width: 65mm;
        }

        h2,
        h4 {
            margin: 0;
            text-align: center;
        }

        hr {
            border: none;
            border-top: 1px dashed black;
            margin: 5px 0;
        }

        .content {
            margin: 5px 0;
        }

        .content div {
            margin: 5px 0;
        }

        @media print {
			body {
				height: auto !important;
				width: 100vw;
			}
			
            .printbody {
                width: 65mm;
                margin: 0;
            }
        }
    </style>
</head>

<body>
	<div class="printbody">
		<center>
			<img src="logo.png?__t=<?= time() ?>" style="width: 200px; margin-top: 0px; margin-bottom: -30px;" alt="Wiguna Perkasa" />
		</center>
		<center>
			<h3>BUKTI PEMBAYARAN</h3>
			<h4>Wifi Wiguna Perkasa</h4>
			<span>Jl. kibadang samaran blok 2 desa bulak, Arjawinangun, Cirebon 45162</span>
			<hr>
		</center>

		<div class="content">
			<?php
			$id = $_GET['id_tagihan'];
			$sql_tampil = "SELECT p.id_pelanggan, p.nama, p.no_hp, t.id_tagihan, t.tagihan, p.alamat, t.status, t.tgl_bayar, t.bulan, t.tahun, k.id_paket   
			FROM tb_pelanggan p 
			INNER JOIN tb_tagihan t ON p.id_pelanggan = t.id_pelanggan 
			INNER JOIN tb_paket k ON k.id_paket = p.id_paket 
			WHERE status = 'LS' AND id_tagihan = '$id'";
			$query_tampil = mysqli_query($koneksi, $sql_tampil);
			while ($data = mysqli_fetch_array($query_tampil, MYSQLI_BOTH)) {
			?>
				<div><strong>Tanggal Bayar:</strong> <?php $tgl = $data['tgl_bayar'];
					echo date("d-M-Y", strtotime($tgl)); ?></div>
				<div><strong>ID Pelanggan:</strong> <?php echo $data['id_pelanggan']; ?></div>
				<div><strong>Nama Pelanggan:</strong> <?php echo $data['nama']; ?></div>
				<div><strong>Tagihan:</strong> <?php echo rupiah($data['tagihan']); ?></div>
				<div><strong>Alamat:</strong><br /> <?php echo $data['alamat']; ?></div>
				<div><strong>Bulan/Tahun:</strong> <?php echo $data['bulan']; ?>/<?php echo $data['tahun']; ?></div>
				<div><strong>Status:</strong> 
					<?php
					$stt = $data['status'];
					if ($stt == 'BL') {
						echo '<span style="color: red;">Belum Bayar</span>';
					} elseif ($stt == 'LS') {
						echo '<span style="color: green;">Lunas</span>';
					}
					?>
				</div>
				<hr>
				<div><strong>Total:</strong> <?php echo rupiah($data['tagihan']); ?></div>
			<?php } ?>
		</div>
	</div>

    <script>
        window.print();
    </script>
</body>

</html>