<?php
// ------------------------------
// read.php — 게시글 읽기 페이지
// ------------------------------
declare(strict_types=1);
session_start();

/* ===== 0) DB 연결 ===== */
$pdo = null;
if (file_exists(__DIR__ . '/db.php')) {
    require __DIR__ . '/db.php';
    if (!($pdo instanceof PDO)) {
        throw new RuntimeException('db.php는 $pdo(PDO)를 정의해야 합니다.');
    }
} else {
    $dsn  = getenv('DB_DSN')  ?: 'sqlite:' . __DIR__ . '/board.sqlite';
    $user = getenv('DB_USER') ?: null;
    $pass = getenv('DB_PASS') ?: null;
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    if (strpos($dsn, 'sqlite:') === 0) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME DEFAULT (datetime('now','localtime'))
            );
        ");
    }
}

/* ===== 1) 유틸 ===== */
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

/* ===== 2) id 파라미터 검증 ===== */
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('잘못된 접근입니다.');
}

/* ===== 3) 글 조회 ===== */
$sql = "SELECT id, title, content, created_at FROM posts WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    die('존재하지 않는 글입니다.');
}
?>
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <title><?= h($post['title']) ?> - 게시글 보기</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: system-ui, sans-serif; margin: 24px; }
    .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .btn { padding:8px 12px; border:1px solid #ccc; background:#fff; cursor:pointer; border-radius:8px; text-decoration:none; }
    .btn.primary { background:#222; color:#fff; border-color:#222; }
    .post { border:1px solid #eee; border-radius:8px; padding:16px; }
    .title { font-size:20px; font-weight:bold; margin-bottom:8px; }
    .meta { color:#666; font-size:14px; margin-bottom:12px; }
    .content { white-space:pre-wrap; }
  </style>
</head>
<body>
  <div class="topbar">
    <h2 style="margin:0">게시글 보기</h2>
    <a class="btn" href="list.php">목록으로</a>
  </div>

  <div class="post">
    <div class="title"><?= h($post['title']) ?></div>
    <div class="meta">작성일: <?= h((string)$post['created_at']) ?></div>
    <div class="content"><?= nl2br(h($post['content'])) ?></div>
  </div>
</body>
</html>
