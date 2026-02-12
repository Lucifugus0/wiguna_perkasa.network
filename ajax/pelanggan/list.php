<?php
require_once '../../inc/koneksi.php';

$cek_pelanggan = $koneksi->query("SELECT * FROM tb_pelanggan");

$data_pelanggan = [];
while ($data = $cek_pelanggan->fetch_assoc()) {
    $nama = $data['nama'];
    $alamat = $data['alamat'];
    $no_hp = $data['no_hp'];
    $no_hp_cadangan = $data['no_hp_cadangan'];

    $data_pelanggan[] = [
        'nama' => $nama,
        'alamat' => $alamat,
        'no_hp' => $no_hp,
        'no_hp_cadangan' => $no_hp_cadangan
    ];
}

header("Content-Type: application/json");
echo json_encode($data_pelanggan);
