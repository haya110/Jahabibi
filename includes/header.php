<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rootPath = (basename(dirname($_SERVER['PHP_SELF'])) == 'pages') ? '../' : './';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MR Jahabibi</title>
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/global.css">
    <?php if ($currentPage == 'menu.php'): ?>
        <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/menu.css">
    <?php elseif (in_array($currentPage, ['customer_dashboard.php', 'admin_dashboard.php'])): ?>
        <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/dashboard.css">
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Feeling+Passionate&family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar" id="navbar">
    <a class="logo" href="<?php echo $rootPath; ?>index.php">
        <img src="<?php echo $rootPath; ?>assets/img/Logo_jorby.png" alt="RR Musubi Logo" class="logo-img">
    </a>
    
    <ul class="nav-links">
        <li><a href="<?php echo $rootPath; ?>index.php"
            class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a></li>
        <li><a href="<?php echo $rootPath; ?>pages/menu.php"
            class="<?= $currentPage === 'menu.php' ? 'active' : '' ?>">Menu</a></li>
        <li><a href="<?php echo $rootPath; ?>pages/about.php"
            class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">About</a></li>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="<?php echo $rootPath; ?>pages/admin_dashboard.php"
                class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>">Admin</a></li>
        <?php endif; ?>
    </ul>

    <ul class="nav-user">
        <li><a href="<?php echo $rootPath; ?>pages/cart.php" class="cart-link" title="Cart">
            <i class="material-icons">shopping_cart</i>
            <span class="cart-badge"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span></a>
        </li>
        <li><a href="<?php echo $rootPath; ?>pages/reviews_ratings.php" title="Reviews">
            <i class="material-icons">star</i></a></li>
        <li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $rootPath; ?>pages/customer_dashboard.php" title="My Account">
                    <i class="material-icons">person</i></a>
            <?php else: ?>
                <a href="<?php echo $rootPath; ?>pages/login.php" title="Log In">
                    <i class="material-icons">person</i></a>
            <?php endif; ?>
            </li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="<?php echo $rootPath; ?>includes/logout.php" class="order-btn" style="padding: 0.5rem 1rem; font-size: 0.8rem;">LOGOUT</a></li>
            <?php else: ?>
                <li><a href="<?php echo $rootPath; ?>pages/menu.php" class="order-btn">ORDER NOW</a></li>
            <?php endif; ?>
        <?php else: ?>
            <li><a href="<?php echo $rootPath; ?>pages/login.php" class="order-btn">LOGIN</a></li>
        <?php endif; ?>
    </ul>
</nav>
<main>