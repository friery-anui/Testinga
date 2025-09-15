<?php
require_once 'config.php';

// create post (form multipart/form-data -> forwarded to upload endpoint if file exists)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_post') {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403); echo 'Forbidden'; exit;
    }
    // CSRF check
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(400); echo 'Invalid CSRF'; exit;
    }
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $category = is_numeric($_POST['category']) ? (int)$_POST['category'] : null;
    $media_url = null;

    if (!$title || !$content) { header('Location: ../public/dashboard.php?error=1'); exit; }

    // If file uploaded, move via upload.php (we can accept media path from upload.php response)
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        // reuse upload logic locally for simplicity
        $up = $_FILES['media'];
        $allowed = ['image/jpeg','image/png','image/gif','video/mp4','video/webm'];
        if (!in_array($up['type'], $allowed) || $up['size'] > 10*1024*1024) {
            header('Location: ../public/dashboard.php?error_upload=1'); exit;
        }
        $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        $targetDir = __DIR__ . '/../public/upload/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $dest = $targetDir . $name;
        if (!move_uploaded_file($up['tmp_name'], $dest)) { header('Location: ../public/dashboard.php?error_upload=1'); exit; }
        $media_url = 'upload/' . $name;
    }

    $slug = preg_replace('/[^a-z0-9]+/i','-', strtolower($title)) . '-' . time();
    try {
        $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, category_id, media_url, created_by) VALUES (:t, :s, :c, :cat, :m, :by)");
        $stmt->execute([':t'=>$title,':s'=>$slug,':c'=>$content,':cat'=>$category,':m'=>$media_url,':by'=>$_SESSION['user']['id']]);
        header('Location: ../public/dashboard.php?created=1');
    } catch (Exception $e) {
        header('Location: ../public/dashboard.php?error=1');
    }
}

// delete post (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_post') {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { http_response_code(403); echo 'Forbidden'; exit; }
    $id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    if (!$id) redirect_public('dashboard.php');
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
    $stmt->execute([':id'=>$id]);
    header('Location: ../public/dashboard.php?deleted=1');
}

// update and other actions can be added similarly (edit form -> action=edit_post)
