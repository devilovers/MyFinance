<?php
include '../../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn, "
    DELETE FROM utang
    WHERE id = '$id'"
);

header('Location: ../../pages/utang.php');
exit;
?>