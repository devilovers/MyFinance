<?php
include '../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $queryCek = mysqli_query($conn, "SELECT nama_target FROM tabungan WHERE id = '$id'");
    
    if (mysqli_num_rows($queryCek) > 0) {
        $data = mysqli_fetch_assoc($queryCek);
        $nama_target = $data['nama_target'];
        $deskripsi_transaksi = "Alokasi Dana Tabungan: " . $nama_target;

        mysqli_begin_transaction($conn);

        try {
            mysqli_query($conn, "DELETE FROM tabungan WHERE id = '$id'");
            mysqli_query($conn, "DELETE FROM transaksi WHERE deskripsi = '$deskripsi_transaksi'");

            mysqli_commit($conn);
            header("Location: ../../pages/tabungan.php?status=success");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            header("Location: ../../pages/tabungan.php?status=error");
            exit;
        }
    } else {
        header("Location: ../../pages/tabungan.php");
        exit;
    }
} else {
    header("Location: ../../pages/tabungan.php");
    exit;
}
?>