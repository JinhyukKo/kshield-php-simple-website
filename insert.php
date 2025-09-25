<?php
session_start();
require __DIR__ . '/db.php';

$title   = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$user_id = $_POST['user_id'] ?? ($_SESSION['user_id'] ?? '');

if ($title === '' || $content === '') {
    die('제목과 내용을 입력하세요.');
}

// FK 검증: user_id가 user 테이블에 실제로 있어야 함
$chk = $pdo->prepare("SELECT 1 FROM `user` WHERE user_id = :u LIMIT 1");
$chk->execute([':u' => $user_id]);
if (!$chk->fetch()) {
    die('작성자(user_id)가 유효하지 않습니다. 먼저 user 테이블에 사용자 한 명을 넣으세요.');
}

// INSERT (파일컬럼은 NULL)
$ins = $pdo->prepare("
  INSERT INTO `post` (title, content, user_id, file_name, file_path, file_size)
  VALUES (:t, :c, :u, NULL, NULL, NULL)
");
$ins->execute([':t'=>$title, ':c'=>$content, ':u'=>$user_id]);

header('Location: list.php');
exit;
