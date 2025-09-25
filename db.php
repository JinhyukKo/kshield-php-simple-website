<?php
// db.php — MySQL(board_db) 연결
$DB_HOST = '127.0.0.1';
$DB_PORT = '3306';
$DB_NAME = 'board_db';
$DB_USER = 'root';      // ← 네 MySQL 계정으로 수정
$DB_PASS = '';          // ← 네 MySQL 비번으로 수정

$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('DB 연결 실패: ' . $e->getMessage());
}
