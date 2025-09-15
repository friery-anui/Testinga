<?php require_once __DIR__.'/../api/config.php'; ?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Đăng nhập</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="auth-card">
  <h2>Đăng nhập</h2>
  <?php if (isset($_GET['error'])): ?><p class="err">Sai thông tin</p><?php endif; ?>
  <form method="post" action="../api/auth.php">
    <input type="hidden" name="action" value="login">
    <label>Tên đăng nhập: <input name="username" required></label>
    <label>Mật khẩu: <input name="password" type="password" required></label>
    <button type="submit">Đăng nhập</button>
  </form>
  <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
</div>
</body>
</html>
