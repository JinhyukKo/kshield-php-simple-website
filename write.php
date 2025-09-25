<?php
session_start();
require __DIR__ . '/db.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// 세션에 user_id가 있으면 사용, 없으면 user 테이블에서 첫 user_id 자동 사용
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    $row = $pdo->query("SELECT user_id FROM `user` ORDER BY id ASC LIMIT 1")->fetch();
    $user_id = $row['user_id'] ?? ''; // user 테이블 비어 있으면 '' (insert에서 에러 나면 메시지로 종료)
}
?>
<!doctype html>
<html lang="ko">
<head><meta charset="utf-8"><title>글쓰기</title></head>
<body style="font-family:system-ui, sans-serif;margin:24px">
  <a href="list.php">&larr; 목록</a>
  <h2>글쓰기</h2>
  <form method="post" action="insert.php">
    <input type="hidden" name="user_id" value="<?= h($user_id) ?>">
    <p>제목<br><input type="text" name="title" style="width:80%" required></p>
    <p>내용<br><textarea name="content" rows="10" style="width:80%" required></textarea></p>
    <p><button type="submit">저장</button></p>
  </form>
</body>
</html>
