<?php
include '../../config/koneksi.php';

$id = $_POST['id'];
$nama_utang = $_POST['nama_utang'];

$jumlah = str_replace(
    '.',
    '',
    $_POST['jumlah']
);

$jatuh_tempo = $_POST['jatuh_tempo'];
$status = $_POST['status'];

mysqli_query($conn, "
    UPDATE utang
    SET
        nama_utang = '$nama_utang',
        jumlah = '$jumlah',
        jatuh_tempo = '$jatuh_tempo',
        status = '$status'
    WHERE id = '$id'
");

header('Location: ../../pages/utang.php');
exit;
?>