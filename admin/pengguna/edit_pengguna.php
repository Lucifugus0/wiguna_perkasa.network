<?php

    if(isset($_GET['kode'])){
        $sql_cek = "SELECT * FROM tb_pengguna WHERE id_pengguna='".$_GET['kode']."'";
        $query_cek = mysqli_query($koneksi, $sql_cek);
        $data_cek = mysqli_fetch_array($query_cek,MYSQLI_BOTH);
    }

	$pengguna = $_SESSION["ses_nama"];
	print_r($pengguna);
?>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<!-- general form elements -->
			<div class="box box-success">
				<div class="box-header with-border">
					<h3 class="box-title">UBAH PENGGUNA</h3>
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
				<!-- form start -->
				<form action="" method="post" enctype="multipart/form-data">
					<div class="box-body">

						<div class="form-group">
							<input type='hidden' class="form-control" name="id_pengguna" value="<?php echo $data_cek['id_pengguna']; ?>"
							 readonly/>
						</div>

						<div class="form-group">
							<label>Nama Pengguna</label>
							<input class="form-control" name="nama_pengguna" value="<?php echo $data_cek['nama_pengguna']; ?>"
							/>
						</div>

						<div class="form-group">
							<label>Username</label>
							<input class="form-control" name="username" value="<?php echo $data_cek['username']; ?>"
							/>
						</div>
						<?php if ($_SESSION["ses_id"] == $data_cek['id_pengguna']) : ?>
						<div class="form-group">
							<label for="exampleInputPassword1">Password</label>
							<input type="password" class="form-control" name="password" id="pass" value="<?php echo $data_cek['password'] ?? $data_cek['password'] ; ?>"
							/>
							<input id="mybutton" onclick="change()" type="checkbox" class="form-checkbox"> Lihat Password
						</div>
						<?php endif; ?>


						<!-- /.box-body -->

						<div class="box-footer">
							<input type="submit" name="Ubah" value="Ubah" class="btn btn-success">
							<a href="?page=MyApp/data_pengguna" title="Kembali" class="btn btn-warning">Batal</a>
						</div>
				</form>
				</div>
				<!-- /.box -->
</section>

<?php

if (isset ($_POST['Ubah'])) {
    $nama_pengguna = $_POST['nama_pengguna'];
    $username = $_POST['username'];
    $id_pengguna = $_POST['id_pengguna'];

    // Cek apakah input password tersedia di POST
    if (isset($_POST['password']) && $_POST['password'] != "") {
        $password = $_POST['password'];
        $sql_ubah = "UPDATE tb_pengguna SET
            nama_pengguna='$nama_pengguna',
            username='$username',
            password='$password'
            WHERE id_pengguna='$id_pengguna'";
    } else {
        // Kalau password tidak dikirim (karena disembunyikan), jangan ubah password
        $sql_ubah = "UPDATE tb_pengguna SET
            nama_pengguna='$nama_pengguna',
            username='$username'
            WHERE id_pengguna='$id_pengguna'";
    }

    $query_ubah = mysqli_query($koneksi, $sql_ubah);

    if ($query_ubah) {
        echo "<script>
        Swal.fire({title: 'Ubah Data Berhasil',text: '',icon: 'success',confirmButtonText: 'OK'
        }).then((result) => {
            if (result.value) {
                window.location = 'index.php?page=MyApp/data_pengguna';
            }
        })</script>";
    } else {
        echo "<script>
        Swal.fire({title: 'Ubah Data Gagal',text: '',icon: 'error',confirmButtonText: 'OK'
        }).then((result) => {
            if (result.value) {
                window.location = 'index.php?page=MyApp/data_pengguna';
            }
        })</script>";
    }
}

?>

<script type="text/javascript">
        function change()
        {
        var x = document.getElementById('pass').type;

        if (x == 'password')
        {
            document.getElementById('pass').type = 'text';
            document.getElementById('mybutton').innerHTML;
        }
        else
        {
            document.getElementById('pass').type = 'password';
            document.getElementById('mybutton').innerHTML;
        }
        }
    </script>