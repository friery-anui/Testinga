<?php
require_once 'config.php';

// Helper: redirect back to public pages
function redirect_public($path) {
    header('Location: ../public/' . $path);
    exit;
}

// REGISTER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    if (!$username || !$password) {
        redirect_public('register.php?error=1');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, email) VALUES (:u, :p, 'user', :e)");
        $stmt->execute([':u'=>$username, ':p'=>$hash, ':e'=>$email]);
        redirect_public('login.php?registered=1');
    } catch (Exception $e) {
        redirect_public('register.php?error=1');
    }
}

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) redirect_public('login.php?error=1');

    $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u'=>$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
        redirect_public('index.php');
    } else {
        redirect_public('login.php?error=1');
    }
}

// LOGOUT
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    redirect_public('index.php');
}
