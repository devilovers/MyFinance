<?php
include '../../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn, "
    DELETE FROM investasi
    WHERE id = '$id'
");

header('Location: ../../pages/investasi.php');
exit;
?>