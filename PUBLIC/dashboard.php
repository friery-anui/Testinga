<?php
require_once __DIR__.'/../api/config.php';
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { header('Location: index.php'); exit; }

// fetch posts & comments
$posts = $pdo->query('SELECT p.*, u.username FROM posts p LEFT JOIN users u ON p.created_by=u.id ORDER BY p.created_at DESC')->fetchAll();
$comments = $pdo->query('SELECT co.*, p.title FROM comments co LEFT JOIN posts p ON co.post_id=p.id ORDER BY co.created_at DESC')->fetchAll();
$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title><link rel="stylesheet" href="styles.css"></head>
<body>
<header class="site-header"><div class="container"><a class="brand" href="index.php">Giải Trí</a> <a href="../api/auth.php?logout=1">Đăng xuất</a></div></header>
<main class="container">
  <h2>Dashboard (Admin)</h2>
  <section class="card">
    <h3>Đăng bài mới</h3>
    <form method="post" action="../api/post.php" enctype="multipart/form-data">
      <input type="hidden" name="action" value="create_post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <label>Tiêu đề: <input name="title" required></label>
      <label>Thể loại:
        <select name="category">
          <option value="">--Chọn--</option>
          <?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label>Media: <input type="file" name="media" accept="image/*,video/*"></label>
      <label>Nội dung: <textarea name="content" required></textarea></label>
      <button type="submit">Đăng</button>
    </form>
  </section>

  <section class="card">
    <h3>Quản lý bài</h3>
    <?php foreach($posts as $p): ?>
      <div class="item">
        <strong><?= htmlspecialchars($p['title']) ?></strong> — <?= htmlspecialchars($p['username']) ?>
        <form method="post" action="../api/post.php" style="display:inline">
          <input type="hidden" name="action" value="delete_post">
          <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
          <button type="submit">Xóa</button>
        </form>
      </div>
    <?php endforeach; ?>
  </section>

  <section class="card">
    <h3>Bình luận</h3>
    <?php foreach($comments as $c): ?>
      <div class="item"><strong><?= htmlspecialchars($c['name'] ?? ($c['user_id'] ? 'User #' . $c['user_id'] : 'Khách')) ?></strong> on <em><?= htmlspecialchars($c['title']) ?></em>
        <form method="post" action="../api/comment.php" style="display:inline">
          <input type="hidden" name="action" value="delete_comment">
          <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
          <button type="submit">Xóa</button>
        </form>
      </div>
    <?php endforeach; ?>
  </section>
</main>
</body>
</html>
