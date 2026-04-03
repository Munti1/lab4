<?php
// pages/logout.php — Muntean Alexandru-Ioan
define('BASE_URL', '/lab4/');
require_once __DIR__ . '/../includes/auth.php';
logout();
header('Location: ' . BASE_URL . 'index.php');
exit;
