<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'id';
}

$current_lang = $_SESSION['lang'] ?? 'id';

$lang_file = __DIR__ . "/../languages/{$current_lang}.php";
if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    $lang = [];
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_finance";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal : " . mysqli_connect_error());
}
?>