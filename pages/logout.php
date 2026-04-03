<?php
// pages/logout.php — Student 1
define('BASE_URL', '/lab4/');
require_once __DIR__ . '/../includes/auth.php';
logout();
header('Location: ' . BASE_URL . 'index.php');
exit;
