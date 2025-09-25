<?php
// ------------------------------
// list.php — 게시글 목록 페이지
// ------------------------------
declare(strict_types=1);
session_start();

/* ===== 0) DB 연결: db.php → ENV → SQLite 순으로 유연하게 시도 ===== */
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
    // SQLite면 테이블 없을 때를 대비해 간단 생성(있으면 무시)
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

/* ===== 2) 목록 조회 =====
   - created_at이 없을 수도 있어 id DESC 폴백 포함
*/
$columns = [];
try {
    // 드라이버별 컬럼 존재 확인
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        $cols = $pdo->query("PRAGMA table_info(posts)")->fetchAll();
        foreach ($cols as $c) $columns[$c['name']] = true;
    } else {
        // MySQL/PostgreSQL 호환 쿼리(대부분 동작)
        $stmt = $pdo->query("SELECT * FROM posts LIMIT 0");
        for ($i=0; $i < $stmt->columnCount(); $i++) {
            $meta = $stmt->getColumnMeta($i);
            if (!empty($meta['name'])) $columns[$meta['name']] = true;
        }
    }
} catch (Throwable $e) {
    // 컬럼 탐색 실패 시 created_at 미확정으로 간주
}

$orderBy = isset($columns['created_at']) ? "created_at DESC, id DESC" : "id DESC";
$sql = "SELECT id, title " . (isset($columns['created_at']) ? ", created_at" : "") . " FROM posts ORDER BY {$orderBy}";
$rows = $pdo->query($sql)->fetchAll();
?>
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <title>게시판 목록</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: system-ui, sans-serif; margin: 24px; }
    .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .btn { padding:8px 12px; border:1px solid #ccc; background:#fff; cursor:pointer; border-radius:8px; text-decoration:none; }
    .btn.primary { background:#222; color:#fff; border-color:#222; }
    table { width:100%; border-collapse:collapse; }
    th, td { border:1px solid #eee; padding:10px; text-align:left; }
    th { background:#fafafa; }
    .empty { color:#888; padding:24px 0; text-align:center; }
  </style>
</head>
<body>
  <div class="topbar">
    <h2 style="margin:0">게시글 목록</h2>
    <a class="btn primary" href="write.php">글쓰기</a>
  </div>

  <?php if (!$rows): ?>
    <div class="empty">게시글이 없습니다.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th style="width:80px;">번호</th>
          <th>제목</th>
          <th style="width:200px;">작성일</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><a href="read.php?id=<?= (int)$r['id'] ?>">
    <?= h($r['title']) ?> </a> </td>
            <td><?= isset($r['created_at']) ? h((string)$r['created_at']) : '' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
