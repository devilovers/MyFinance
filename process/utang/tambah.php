<?php
include '../../config/koneksi.php';

$nama_utang = $_POST['nama_utang'];

$jumlah = str_replace(
    '.',
    '',
    $_POST['jumlah']
);

$jatuh_tempo = $_POST['jatuh_tempo'];

mysqli_query($conn, "
    INSERT INTO utang
    (
        nama_utang,
        jumlah,
        jatuh_tempo,
        status,
        created_at
    )
    VALUES
    (
        '$nama_utang',
        '$jumlah',
        '$jatuh_tempo',
        'Belum Lunas',
        NOW()
    )
");

header('Location: ../../pages/utang.php');
exit;
?>