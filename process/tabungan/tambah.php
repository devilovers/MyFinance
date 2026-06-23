<?php
include '../../config/koneksi.php';

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
    INSERT INTO tabungan
    (
        nama_target,
        target,
        terkumpul,
        tanggal,
        created_at
    )
    VALUES
    (
        '$nama_target',
        '$target',
        '$terkumpul',
        '$tanggal',
        NOW()
    )
");

header('Location: ../../pages/tabungan.php');
exit;
?>