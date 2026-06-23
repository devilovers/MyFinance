<?php
include '../../config/koneksi.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    exit;
}

$result = mysqli_query($conn, "SELECT is_pinned FROM tabungan WHERE id = '$id'");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
    exit;
}

$current_status = $row['is_pinned'];
$new_status = $current_status ? 0 : 1;

mysqli_query($conn, "
    UPDATE tabungan
    SET is_pinned = '$new_status'
    WHERE id = '$id'
");

echo json_encode(['success' => true, 'is_pinned' => $new_status]);
exit;
?>