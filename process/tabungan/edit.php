<?php
include '../../config/koneksi.php';

$id = $_POST['id'];

$nama_target = $_POST['nama_target'];

$target = str_replace(
    '.',
    '',
    $_POST['target']
);

$terkumpul = str_replace(
    '.',
    '',
    $_POST['terkumpul']
);

$tanggal = $_POST['tanggal'];

mysqli_query($conn, "
    UPDATE tabungan
    SET
        nama_target = '$nama_target',
        target = '$target',
        terkumpul = '$terkumpul',
        tanggal = '$tanggal',
        updated_at = NOW()
    WHERE id = '$id'
");

header('Location: ../../pages/tabungan.php');
exit;
?>