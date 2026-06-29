<?php
include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_target = mysqli_real_escape_string($conn, $_POST['nama_target'] ?? '');
    $target_dana = intval($_POST['target'] ?? 0);
    $dana_terkumpul = intval($_POST['terkumpul'] ?? 0);
    $tanggal_target = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));

    if ($dana_terkumpul <= 0) {
        $queryTabungan = "INSERT INTO tabungan (nama_target, target, terkumpul, tanggal, created_at) 
                          VALUES ('$nama_target', '$target_dana', 0, '$tanggal_target', NOW())";
        mysqli_query($conn, $queryTabungan);
        header("Location: ../../pages/tabungan.php?status=success");
        exit;
    }

    $querySaldo = mysqli_query($conn, "
        SELECT 
            SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) - 
            SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) AS total_saldo 
        FROM transaksi
    ");
    $dataSaldo = mysqli_fetch_assoc($querySaldo);
    $total_saldo_sekarang = intval($dataSaldo['total_saldo'] ?? 0);

    if ($dana_terkumpul > $total_saldo_sekarang) {
        header("Location: ../../pages/tabungan.php?status=insufficient_balance");
        exit;
    }

    mysqli_begin_transaction($conn);

    try {
        $queryTabungan = "INSERT INTO tabungan (nama_target, target, terkumpul, tanggal, created_at) 
                          VALUES ('$nama_target', '$target_dana', '$dana_terkumpul', '$tanggal_target', NOW())";
        mysqli_query($conn, $queryTabungan);

        $deskripsi_transaksi = "Alokasi Dana Tabungan: " . $nama_target;
        $queryPotongSaldo = "INSERT INTO transaksi (tanggal, jenis, jumlah, deskripsi, created_at) 
                             VALUES ('$tanggal_target', 'Pengeluaran', '$dana_terkumpul', '$deskripsi_transaksi', NOW())";
        mysqli_query($conn, $queryPotongSaldo);

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
?>