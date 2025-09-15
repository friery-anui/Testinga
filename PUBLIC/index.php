<?php
require_once __DIR__ . '/../api/config.php';

// pagination & search
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$limit = 8;
$offset = ($page-1)*$limit;
$q = trim($_GET['q'] ?? '');
$cat = isset($_GET['cat']) && is_numeric($_GET['cat']) ? (int)$_GET['cat'] : null;

$where = [];
$params = [];
if ($q) { $where[] = "(p.title LIKE :q OR p.content LIKE :q)"; $params[':q'] = "%$q%"; }
if ($cat) { $where[] = "p.category_id = :cat"; $params[':cat'] = $cat; }
$whereSql = $where ? 'WHERE '.implode(' AND ',$where) : '';

$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM posts p $whereSql");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();

$stmt = $pdo->prepare("SELECT p.*, c.name as category, u.username FROM posts p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN users u ON p.created_by=u.id $whereSql ORDER BY p.created_at DESC LIMIT :offset, :limit");
foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
$stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
$stmt->bindValue(':limit',$limit,PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Giải trí - Trang chủ</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="site-header">
  <div class="container">
    <h1 class="brand"><a href="index.php">Giải Trí</a></h1>
    <nav class="nav">
      <form method="get" action="index.php" class="search-form">
        <input name="q" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($q) ?>">
        <button type="submit">Tìm</button>
      </form>
      <?php if (!empty($_SESSION['user'])): ?>
        <span>Xin chào, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?> <a href="dashboard.php">Dashboard</a> <?php endif; ?>
        <a href="../api/auth.php?logout=1">Đăng xuất</a>
      <?php else: ?>
        <a href="login.php">Đăng nhập</a>
        <a href="register.php">Đăng ký</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container main-grid">
  <aside class="sidebar">
    <h3>Thể loại</h3>
    <ul>
      <li><a href="index.php">Tất cả</a></li>
      <?php foreach ($categories as $c): ?>
        <li><a href="index.php?cat=<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <section class="content">
    <div class="posts-grid">
      <?php foreach ($posts as $p): ?>
        <article class="post-card">
          <?php if ($p['media_url']): ?>
            <?php if (preg_match('/\\.(mp4|webm)$/i', $p['media_url'])): ?>
              <video src="<?= htmlspecialchars($p['media_url']) ?>" controls muted style="width:100%; height:150px; object-fit:cover"></video>
            <?php else: ?>
              <img src="<?= htmlspecialchars($p['media_url']) ?>" alt="" style="width:100%; height:150px; object-fit:cover">
            <?php endif; ?>
          <?php endif; ?>
          <h3><a href="post.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
          <p class="meta"><?= htmlspecialchars($p['category']) ?> • <?= $p['created_at'] ?></p>
          <p><?= nl2br(htmlspecialchars(substr(strip_tags($p['content']),0,180))) ?>...</p>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="pagination">
      <?php $pages = max(1, ceil($total / $limit));
      for ($i=1;$i<=$pages;$i++): ?>
        <a class="page-link" href="index.php?page=<?= $i ?><?= $q ? '&q='.urlencode($q):'' ?><?= $cat ? '&cat='.urlencode($cat):'' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </section>
</main>

<footer class="site-footer"><div class="container">© <?= date('Y') ?> Giải trí</div></footer>
<script src="app.js"></script>
</body>
</html>
