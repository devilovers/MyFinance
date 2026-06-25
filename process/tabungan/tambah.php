<?php
include '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_target = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $target_dana = intval($_POST['target'] ?? 0);
    $dana_terkumpul = intval($_POST['terkumpul'] ?? 0);
    $tanggal_target = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));

    $queryTabungan = "INSERT INTO tabungan (nama_target, target, terkumpul, tanggal, created_at) 
                      VALUES ('$nama_target', '$target_dana', '$dana_terkumpul', '$tanggal_target', NOW())";
    
    if (mysqli_query($conn, $queryTabungan)) {
        header("Location: ../../pages/tabungan.php?status=success");
    } else {
        header("Location: ../../pages/tabungan.php?status=error");
    }
} else {
    header("Location: ../../pages/tabungan.php");
}
?>