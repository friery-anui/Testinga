<?php
require_once "../api/config.php";
$stmt = $pdo->query("SELECT p.*, c.name as category, u.username 
                     FROM posts p 
                     LEFT JOIN categories c ON p.category_id=c.id
                     LEFT JOIN users u ON p.created_by=u.id
                     ORDER BY created_at DESC LIMIT 10");
$posts = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Trang Giải trí</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <h1>Trang Giải Trí</h1>
  <nav>
    <a href="index.php">Home</a>
    <?php if(isset($_SESSION['user'])): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="../api/auth.php?logout=1">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main>
  <?php foreach($posts as $p): ?>
    <article>
      <h2><a href="post.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a></h2>
      <p><?= substr(strip_tags($p['content']),0,150) ?>...</p>
      <small>By <?= $p['username'] ?> | <?= $p['category'] ?> | <?= $p['created_at'] ?></small>
    </article>
  <?php endforeach; ?>
</main>
</body>
</html>
