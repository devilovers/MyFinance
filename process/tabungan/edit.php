<?php
include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nama_target = mysqli_real_escape_string($conn, $_POST['nama_target']);
    $target = str_replace('.', '', $_POST['target']);
    $terkumpul = str_replace('.', '', $_POST['terkumpul']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);

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
} else {
    header('Location: ../../pages/tabungan.php');
}
?>