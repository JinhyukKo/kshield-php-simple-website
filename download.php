<?php
require_once 'config.php';

$file_id = $_GET['id'];

$sql = "SELECT * FROM files WHERE id = $file_id";
$result = $pdo->query($sql);
$file = $result->fetch();

$file_path = 'uploads/' . $file['filename'];

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file['real_filename'] . '"');
header('Content-Length: ' . filesize($file_path));

readfile($file_path);
?>