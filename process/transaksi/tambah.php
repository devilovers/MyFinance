<?php
include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = $_POST['jenis'];
    $kategori = $_POST['kategori'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $query = "INSERT INTO transaksi (jenis, kategori, metode_pembayaran, jumlah, tanggal, deskripsi) 
              VALUES ('$jenis', '$kategori', '$metode_pembayaran', '$jumlah', '$tanggal', '$deskripsi')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../pages/transaksi.php?status=success_tambah");
    } else {
        header("Location: ../../pages/transaksi.php?status=failed_tambah");
    }
} else {
    header("Location: ../../pages/transaksi.php");
}
?>