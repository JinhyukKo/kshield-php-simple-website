<?php
session_start();
require __DIR__ . '/db.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$sql = "
  SELECT p.id, p.title, p.created_at, p.user_id, u.name AS author_name
  FROM `post` p
  LEFT JOIN `user` u ON p.user_id = u.user_id
  ORDER BY p.created_at DESC, p.id DESC
";
$rows = $pdo->query($sql)->fetchAll();
?>
<!doctype html>
<html lang="ko">
<head><meta charset="utf-8"><title>게시글 목록</title></head>
<body style="font-family:system-ui, sans-serif;margin:24px">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h2 style="margin:0">게시글 목록</h2>
    <a href="write.php" style="padding:8px 12px;border-radius:6px;background:#222;color:#fff;text-decoration:none;">글쓰기</a>
  </div>

  <?php if (!$rows): ?>
    <p style="color:#666">게시글이 없습니다.</p>
  <?php else: ?>
    <table border="1" cellpadding="8" style="border-collapse:collapse;width:100%">
      <thead style="background:#f7f7f7">
        <tr><th style="width:80px">번호</th><th>제목</th><th style="width:160px">작성자</th><th style="width:180px">작성일</th></tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><a href="read.php?id=<?= (int)$r['id'] ?>"><?= h($r['title']) ?></a></td>
          <td><?= h($r['author_name'] ?: $r['user_id']) ?></td>
          <td><?= h($r['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
