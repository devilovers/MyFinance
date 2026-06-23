<?php
include '../../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn, "
    DELETE FROM tabungan
    WHERE id = '$id'
");

header('Location: ../../pages/tabungan.php');
exit;
?>