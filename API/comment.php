<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_comment') {
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $comment = trim($_POST['comment'] ?? '');
    $name = trim($_POST['name'] ?? null);
    if (!$post_id || !$comment) { header('Location: ../public/post.php?id=' . $post_id . '&error=1'); exit; }
    $user_id = $_SESSION['user']['id'] ?? null;
    $stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, name, comment) VALUES (:p, :u, :n, :c)');
    $stmt->execute([':p'=>$post_id, ':u'=>$user_id, ':n'=>$name, ':c'=>$comment]);
    header('Location: ../public/post.php?id=' . $post_id . '&commented=1');
}

// Admin delete comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo 'Forbidden'; exit; }
    $id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = :id');
        $stmt->execute([':id'=>$id]);
    }
    header('Location: ../public/dashboard.php');
}
