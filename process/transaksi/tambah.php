<?php
include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $jenis = mysqli_real_escape_string(
        $conn,
        $_POST['jenis']
    );

    $kategori = mysqli_real_escape_string(
        $conn,
        $_POST['kategori']
    );

    $jumlah = (int) $_POST['jumlah'];

    $tanggal = mysqli_real_escape_string(
        $conn,
        $_POST['tanggal']
    );

    $deskripsi = mysqli_real_escape_string(
        $conn,
        $_POST['deskripsi']
    );

    $query = mysqli_query(
        $conn,
        "INSERT INTO transaksi
        (
            jenis,
            kategori,
            jumlah,
            deskripsi,
            tanggal
        )
        VALUES
        (
            '$jenis',
            '$kategori',
            '$jumlah',
            '$deskripsi',
            '$tanggal'
        )"
    );

    if ($query) {
        header('Location: ../../pages/transaksi.php');
        exit;
    }

    die('Gagal menyimpan transaksi : ' . mysqli_error($conn));
}
?>