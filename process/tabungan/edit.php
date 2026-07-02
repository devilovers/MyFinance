<?php
include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nama_target = mysqli_real_escape_string($conn, $_POST['nama_target']);
    $target = str_replace('.', '', $_POST['target']);
    $terkumpul_baru = str_replace('.', '', $_POST['terkumpul']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);

    $query_lama = mysqli_query($conn, "SELECT terkumpul FROM tabungan WHERE id = '$id'");
    $data_lama = mysqli_fetch_assoc($query_lama);
    $terkumpul_lama = $data_lama['terkumpul'] ?? 0;

    $selisih = $terkumpul_baru - $terkumpul_lama;

    if ($selisih != 0) {
        if ($selisih > 0) {
            $jenis = 'Pengeluaran';
            $jumlah_transaksi = $selisih;
            $deskripsi = "Penambahan tabungan untuk: " . $nama_target;
        } else {
            $jenis = 'Pemasukan';
            $jumlah_transaksi = abs($selisih);
            $deskripsi = "Penarikan/pengurangan tabungan dari: " . $nama_target;
        }

        $kategori = 'Tabungan'; 
        $metode_pembayaran = 'Cash'; 
        $tanggal_sekarang = date('Y-m-d');

        mysqli_query($conn, "
            INSERT INTO transaksi (jenis, kategori, metode_pembayaran, jumlah, tanggal, deskripsi)
            VALUES ('$jenis', '$kategori', '$metode_pembayaran', '$jumlah_transaksi', '$tanggal_sekarang', '$deskripsi')
        ");
    }

    mysqli_query($conn, "
        UPDATE tabungan
        SET
            nama_target = '$nama_target',
            target = '$target',
            terkumpul = '$terkumpul_baru',
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