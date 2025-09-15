<?php require_once __DIR__.'/../api/config.php'; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Đăng ký</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="auth-card">
  <h2>Đăng ký</h2>
  <?php if (isset($_GET['error'])): ?><p class="err">Không thể đăng ký</p><?php endif; ?>
  <form method="post" action="../api/auth.php">
    <input type="hidden" name="action" value="register">
    <label>Tên đăng nhập: <input name="username" required></label>
    <label>Email: <input name="email" type="email"></label>
    <label>Mật khẩu: <input name="password" type="password" required></label>
    <button type="submit">Đăng ký</button>
  </form>
  <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</div>
</body>
</html>
