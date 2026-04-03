<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_URL')) define('BASE_URL', '/lab4/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'BarberCo Studio' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>

<nav class="navbar">
    <a class="navbar-brand" href="<?= BASE_URL ?>index.php">
        <span class="brand-icon">✦</span> BarberCo
    </a>
    <ul class="nav-links">
        <li><a href="<?= BASE_URL ?>index.php">Home</a></li>
        <li><a href="<?= BASE_URL ?>pages/services.php">Services</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?= BASE_URL ?>pages/book.php" class="nav-cta">Book Now</a></li>
            <li><a href="<?= BASE_URL ?>pages/my_appointments.php">My Appointments</a></li>
            <li><a href="<?= BASE_URL ?>pages/logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="<?= BASE_URL ?>pages/login.php">Login</a></li>
            <li><a href="<?= BASE_URL ?>pages/register.php" class="nav-cta">Register</a></li>
        <?php endif; ?>
    </ul>
    <button class="hamburger" onclick="this.closest('nav').classList.toggle('open')">☰</button>
</nav>
