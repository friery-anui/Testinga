<?php
require_once __DIR__ . '/../api/config.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare('SELECT p.*, c.name as category, u.username FROM posts p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN users u ON p.created_by=u.id WHERE p.id=:id LIMIT 1');
$stmt->execute([':id'=>$id]);
$post = $stmt->fetch();
if (!$post) { header('Location: index.php'); exit; }

$cstmt = $pdo->prepare('SELECT co.*, u.username FROM comments co LEFT JOIN users u ON co.user_id=u.id WHERE co.post_id=:id ORDER BY co.created_at ASC');
$cstmt->execute([':id'=>$id]);
$comments = $cstmt->fetchAll();
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($post['title']) ?> — Giải trí</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="site-header"><div class="container"><a class="brand" href="index.php">← Giải Trí</a></div></header>
<main class="container">
  <article class="post-detail">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p class="meta"><?= htmlspecialchars($post['category']) ?> • <?= $post['created_at'] ?> • By <?= htmlspecialchars($post['username']) ?></p>
    <?php if ($post['media_url']): ?>
      <?php if (preg_match('/\\.(mp4|webm)$/i', $post['media_url'])): ?>
        <video controls src="<?= htmlspecialchars($post['media_url']) ?>"></video>
      <?php else: ?>
        <img src="<?= htmlspecialchars($post['media_url']) ?>" alt="">
      <?php endif; ?>
    <?php endif; ?>
    <div class="content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
  </article>

  <section class="comments-box">
    <h3>Bình luận</h3>
    <?php if (!empty($_SESSION['user'])): ?>
      <form method="post" action="../api/comment.php">
        <input type="hidden" name="action" value="create_comment">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        <label>Tên (nếu muốn): <input type="text" name="name" placeholder="Tên"></label>
        <label>Bình luận:<textarea name="comment" required></textarea></label>
        <button type="submit">Gửi</button>
      </form>
    <?php else: ?>
      <p><a href="login.php">Đăng nhập</a> để bình luận.</p>
    <?php endif; ?>

    <div id="comment-list">
      <?php foreach ($comments as $c): ?>
        <div class="comment">
          <strong><?= htmlspecialchars($c['username'] ?? $c['name'] ?? 'Khách') ?></strong>
          <small><?= $c['created_at'] ?></small>
          <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>
</body>
</html>
