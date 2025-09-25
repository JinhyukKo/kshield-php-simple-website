<?php
session_start();
require __DIR__ . '/db.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die('잘못된 접근');

$sql = "
  SELECT p.id, p.title, p.content, p.created_at, p.user_id, u.name AS author_name
  FROM `post` p
  LEFT JOIN `user` u ON p.user_id = u.user_id
  WHERE p.id = :id
  LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();
if (!$post) die('존재하지 않는 글입니다.');
?>
<!doctype html>
<html lang="ko">
<head><meta charset="utf-8"><title><?= h($post['title']) ?></title></head>
<body style="font-family:system-ui, sans-serif;margin:24px">
  <a href="list.php" style="text-decoration:none;color:#555">&larr; 목록</a>
  <h1><?= h($post['title']) ?></h1>
  <p style="color:#666">작성자: <?= h($post['author_name'] ?: $post['user_id']) ?> · 작성일: <?= h($post['created_at']) ?></p>
  <div style="white-space:pre-wrap;border:1px solid #eee;padding:16px;border-radius:6px;"><?= nl2br(h($post['content'])) ?></div>
</body>
</html>
