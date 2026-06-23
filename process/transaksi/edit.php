<?php
include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = (int) $_POST['id'];

    $jenis = $_POST['jenis'];
    $kategori = $_POST['kategori'];

    $jumlah = str_replace('.', '', $_POST['jumlah']);

    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];

    $query = mysqli_query($conn, "
        UPDATE transaksi
        SET
            jenis='$jenis',
            kategori='$kategori',
            jumlah='$jumlah',
            deskripsi='$deskripsi',
            tanggal='$tanggal'
        WHERE id='$id'
    ");

    header('Location: ../../pages/transaksi.php');
    exit;
}
?>