<?php
// -------------------------------------
// write.php — 글쓰기(폼 + 저장) 단일 파일
// -------------------------------------
declare(strict_types=1);
session_start();

/* ===== 0) DB 연결: db.php → ENV → SQLite 순 ===== */
$pdo = null;
if (file_exists(__DIR__ . '/db.php')) {
    require __DIR__ . '/db.php'; // 여기서 $pdo가 만들어진다고 가정
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
    // SQLite면 posts 없을 때 자동 생성
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

/* ===== 2) 스키마 탐지: user_id, created_at 유무 ===== */
$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
$hasUserId = false;
$hasCreatedAt = false;

try {
    if ($driver === 'sqlite') {
        $cols = $pdo->query("PRAGMA table_info(posts)")->fetchAll();
        foreach ($cols as $c) {
            if ($c['name'] === 'user_id')    $hasUserId = true;
            if ($c['name'] === 'created_at') $hasCreatedAt = true;
        }
    } else {
        $stmt = $pdo->query("SELECT * FROM posts LIMIT 0");
        for ($i=0; $i < $stmt->columnCount(); $i++) {
            $meta = $stmt->getColumnMeta($i);
            $name = $meta['name'] ?? '';
            if ($name === 'user_id')    $hasUserId = true;
            if ($name === 'created_at') $hasCreatedAt = true;
        }
    }
} catch (Throwable $e) {
    // 컬럼 탐색 실패는 무시(디폴트 false)
}

/* ===== 3) POST 처리 (저장) ===== */
$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim((string)($_POST['title'] ?? ''));
    $content = trim((string)($_POST['content'] ?? ''));

    if ($title === '')   $errors[] = '제목을 입력하세요.';
    if ($content === '') $errors[] = '내용을 입력하세요.';

    if (!$errors) {
        // user_id가 필요하면 users에서 최소 id를 하나 찾아 자동 매핑
        $userId = null;
        if ($hasUserId) {
            try {
                $u = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1")->fetch();
                if ($u) $userId = (int)$u['id'];
            } catch (Throwable $e) {
                // users 테이블 없거나 실패 → 그대로 null 유지
            }
        }

        try {
            if ($hasUserId && $userId !== null) {
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (:uid, :t, :c)");
                $stmt->execute([':uid'=>$userId, ':t'=>$title, ':c'=>$content]);
            } elseif ($hasUserId && $userId === null) {
                // 스키마가 user_id NOT NULL일 수 있으므로 try-catch로 한 번 더 시도
                // 만약 NOT NULL이면 예외가 나고, 아래 catch에서 에러 안내
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (NULL, :t, :c)");
                $stmt->execute([':t'=>$title, ':c'=>$content]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO posts (title, content) VALUES (:t, :c)");
                $stmt->execute([':t'=>$title, ':c'=>$content]);
            }
            // 저장 성공 → 목록으로
            header('Location: list.php');
            exit;
        } catch (Throwable $e) {
            // user_id 제약/외래키 등으로 실패했을 가능성
            $errors[] = '저장 중 오류: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <title>글쓰기</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: system-ui, sans-serif; margin: 24px; }
    .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .btn { padding:10px 14px; border:1px solid #ccc; background:#fff; cursor:pointer; border-radius:8px; text-decoration:none; }
    .btn.primary { background:#222; color:#fff; border-color:#222; position:fixed; right:24px; bottom:24px; }
    .field { display:flex; flex-direction:column; gap:8px; margin-bottom:16px; max-width:720px; }
    input[type="text"], textarea { width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:16px; }
    textarea { min-height: 240px; resize: vertical; }
    .errors { color:#c0392b; margin-bottom:12px; }
  </style>
</head>
<body>
  <div class="topbar">
    <h2 style="margin:0">글쓰기</h2>
    <a class="btn" href="list.php">목록으로</a>
  </div>

  <?php if ($errors): ?>
    <div class="errors">
      <?php foreach ($errors as $e): ?>
        <div><?= h($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="field">
      <label for="title">제목</label>
      <input id="title" name="title" type="text" maxlength="200" value="<?= h($title) ?>" required>
    </div>
    <div class="field">
      <label for="content">내용</label>
      <textarea id="content" name="content" required><?= h($content) ?></textarea>
    </div>
    <button class="btn primary" type="submit">등록</button>
  </form>
</body>
</html>
