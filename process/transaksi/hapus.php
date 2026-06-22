<?php
include '../../config/koneksi.php';

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    mysqli_query(
        $conn,
        "DELETE FROM transaksi WHERE id = $id"
    );
}

header('Location: ../../pages/transaksi.php');
exit;
?>