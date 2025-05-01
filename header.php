<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="service.php">Services</a>
        <a href="shop.php">Shop</a>
        <a href="contact.php">Contact</a>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="account.php">Account</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Sign In</a>
        <?php endif; ?>

        <a href="cart.php">Cart</a>
    </nav>
    <a href="index.php" class="nav_logo">
        <img src="assets/images/logo.png" alt="Logo">
    </a>
</header>
