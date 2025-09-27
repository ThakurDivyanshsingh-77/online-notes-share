<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Notes Sharing</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header>
    <h1>📚 Online Notes Sharing</h1>
    <nav>
        <a href="index.php">Home</a>
        <?php if(isset($_SESSION['user'])): ?>
            <a href="upload.php"><i class="fas fa-upload"></i> Upload Notes</a>
            <a href="my_notes.php"><i class="fas fa-book"></i> My Notes</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php if($_SESSION['user']['role'] === 'admin'): ?>
                <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
            <?php endif; ?>
            <button id="theme-toggle" title="Toggle Dark Mode">
                <i class="fas fa-moon"></i>
            </button>
        <?php else: ?>
            <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
    </nav>
</header>
<main>
