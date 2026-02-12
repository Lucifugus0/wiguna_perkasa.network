<?php
session_start();
require_once '../../inc/routerosapi.class.php';
require_once '../../inc/koneksi.php';

$API = new RouterosAPI();
$API->port = $router_port;
if (!$API->connect($router_host, $router_username, $router_password)) {
    die(http_response_code(500));
}

function get_default_value($field, $value) {
    if ($field == "hp_otomatis")
        return empty($value) ? "0" : $value;

    return $value;
}

if ($_POST) {
    $id = @$_POST['id_pelanggan'];

    $cek_pelanggan = $koneksi->query("SELECT * FROM tb_pelanggan WHERE id_pelanggan = '$id'");
    if (mysqli_num_rows($cek_pelanggan) == 0)
        die(http_response_code(404));
    $data_pelanggan = $cek_pelanggan->fetch_assoc();
    
    $hotspot_user = $API->comm("/ip/hotspot/user/print", [
		'?name' => $data_pelanggan['email']
	]);

    $field = @$_POST['field'];
    $value = @$_POST['value'];
    $value = get_default_value($field, $value);

    $allowed_field = [
        "nama",
        "mac_modem",
        "ip_modem",
        "teknisi",
        "titik_odp",
        "alamat",
        "patokan",
        "no_hp",
        "no_hp_cadangan",
        "email",
        "password",
        "hp_otomatis"
    ];

    $fieldCustoms = [
        "nama" => "Nama Lengkap",
        "mac_modem" => "MAC Modem",
        "ip_modem" => "IP Modem",
        "teknisi" => "Teknisi",
        "titik_odp" => "Titik ODP",
        "alamat" => "Alamat",
        "patokan" => "Patokan",
        "no_hp" => "No HP",
        "no_hp_cadangan" => "No HP Cadangan",
        "email" => "E-Mail",
        "password" => "Password",
        "hp_otomatis" => "HP Otomatis"
    ];
    $fieldCustom = $fieldCustoms[$field];
    
    if (!in_array($field, $allowed_field))
        die(http_response_code(400));

    if ($field == "email") {
        $exist_hotspot_user = $API->comm("/ip/hotspot/user/print", [
            '?name' => $value
        ]);
        if ($data_pelanggan['email'] != $value && count($exist_hotspot_user) >= 1)
            die(http_response_code(409));

        $API->comm("/ip/hotspot/user/set", [
            '.id' => $hotspot_user[0]['.id'],
            'name' => $value
        ]);
    }

    if ($field == "password") {
        $API->comm("/ip/hotspot/user/set", [
            '.id' => $hotspot_user[0]['.id'],
            'password' => $value
        ]);
    }

    $aktifitas = "UBAH PELANGGAN".PHP_EOL.PHP_EOL
        ."$fieldCustom: $value";
    mysqli_query($koneksi, "INSERT INTO tb_log (pengguna, aktifitas) VALUES ('".$_SESSION['ses_nama']."', '".$aktifitas."')");

    $update = $koneksi->query("UPDATE tb_pelanggan SET $field = '$value' WHERE id_pelanggan = '$id'");
}
