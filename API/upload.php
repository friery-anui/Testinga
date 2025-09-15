<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) json_response(['error'=>'No file'],400);

$file = $_FILES['file'];
$allowed = ['image/jpeg','image/png','image/gif','video/mp4','video/webm'];
if (!in_array($file['type'], $allowed)) json_response(['error'=>'Invalid type'],400);
if ($file['size'] > 10*1024*1024) json_response(['error'=>'File too large'],400);

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$name = bin2hex(random_bytes(8)).'.'.$ext;
$target = __DIR__.'/../public/upload/';
if (!is_dir($target)) mkdir($target,0755,true);
$dest = $target.$name;
if (!move_uploaded_file($file['tmp_name'],$dest)) json_response(['error'=>'Upload failed'],500);

json_response(['success'=>true,'path'=>'upload/'.$name]);
