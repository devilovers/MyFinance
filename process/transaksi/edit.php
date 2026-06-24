<?php
include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $jenis = $_POST['jenis'];
    $kategori = $_POST['kategori'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $query = "UPDATE transaksi SET 
                jenis = '$jenis', 
                kategori = '$kategori', 
                metode_pembayaran = '$metode_pembayaran', 
                jumlah = '$jumlah', 
                tanggal = '$tanggal', 
                deskripsi = '$deskripsi' 
              WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../pages/transaksi.php?status=success_edit");
    } else {
        header("Location: ../../pages/transaksi.php?status=failed_edit");
    }
} else {
    header("Location: ../../pages/transaksi.php");
}
?>