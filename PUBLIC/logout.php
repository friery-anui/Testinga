<?php
require_once __DIR__ . '/../api/config.php';
session_unset(); session_destroy();
header('Location: index.php');
