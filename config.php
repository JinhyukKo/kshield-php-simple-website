<?php
session_start(); // 세션 시작

$host = 'localhost'; // 호스트 주소(이름)
$dbname = 'board_system'; // db이름
$username = 'root'; // db user이름
$password = '1234'; // db password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("error: " . $e->getMessage());
}
?>