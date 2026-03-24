<?php
// includes/header.php — Shared HTML <head> + navigation
// $pageTitle should be set in each page before including this
if (!isset($pageTitle)) $pageTitle = SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | <?= SITE_NAME ?></title>
    <meta name="description" content="StayEase – Book premium hotels across Nepal with ease.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
</head>
<body>

<!-- ── Navigation ──────────────────────────────────────── -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="<?= SITE_URL ?>/index.php" class="nav-logo">
            <i class="fa-solid fa-key"></i>
            Stay<span>Ease</span>
        </a>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
            <li><a href="<?= SITE_URL ?>/hotels.php">Hotels</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="<?= SITE_URL ?>/my-bookings.php">My Bookings</a></li>
                <?php if (isAdmin()): ?>
                <li><a href="<?= SITE_URL ?>/admin/dashboard.php">Admin</a></li>
                <?php endif; ?>
                <li>
                    <a href="<?= SITE_URL ?>/logout.php" class="btn-nav-outline">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </li>
            <?php else: ?>
                <li><a href="<?= SITE_URL ?>/login.php" class="btn-nav-outline">Login</a></li>
                <li><a href="<?= SITE_URL ?>/register.php" class="btn-nav-filled">Register</a></li>
            <?php endif; ?>
        </ul>

        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
<!-- flash messages -->
<div class="flash-container">
    <?= flash('success') ?>
    <?= flash('error') ?>
    <?= flash('info') ?>
</div>
