<?php
session_start();
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ShopBolt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/easy.css">
</head>
<body>


<header>
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="service.php">Services</a>
        <a href="about.php">About</a>
        <a href="shop.php">Shop</a>
        <a href="contact.php">Contact</a>
        <?php if ($is_logged_in): ?>
            <a href="account.php">Account</a>
            <a href="cart.php">Cart</a>
        <?php else: ?>
            <a href="login.php">Sign In</a>
        <?php endif; ?>
    </nav>
    <a href="index.php" class="nav_logo">
        <img src="assets/images/logo.png" alt="Logo">
    </a>
</header>

<section class="container my-5 py-5">
    <div class="text-center">
        <h3>About ShopBolt</h3>
        <hr class="mx-auto w-25">
        <p class="lead w-75 mx-auto">
            Welcome to <strong>ShopBolt</strong> – your ultimate destination for hassle-free online shopping. We are dedicated to providing quality products, fast shipping, and exceptional customer service.
        </p>
        <p class="w-75 mx-auto">
            Our journey began with a vision to make everyday shopping simple and enjoyable. From the latest trends to everyday essentials, we offer a curated selection of items at competitive prices.
        </p>
    </div>
</section>


<footer id="contact" class="bg-light text-dark py-4">
    <div class="container">
        <div class="text-center mb-4">
            <h4>Join Our Mailing List</h4>
            <form class="d-flex justify-content-center gap-2 mt-3">
                <input type="email" class="form-control w-50" placeholder="Enter your email" required>
                <button type="submit" class="btn btn-primary">Get Started</button>
            </form>
        </div>
        <div class="row text-start">
            <div class="col-md-4">
                <img src="assets/images/logo.png" alt="footer-logo" class="mb-2" style="max-width: 150px;">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Magnam, adipisci?</p>
                <div>
                    <a href="#" class="me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="me-2"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
            <div class="col-md-2">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="#">Portfolio</a></li>
                    <li><a href="#">Blogs</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <h5>Support</h5>
                <ul class="list-unstyled">
                    <li><a href="#">FAQ</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="#">Team</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Info</h5>
                <ul class="list-unstyled">
                    <li>Email: youname@gmail.com</li>
                    <li>Phone: +123-456-7890</li>
                    <li>Hours: 8AM - 11PM, Sunday Closed</li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-4">
            <p>&copy; 2025 ShopBolt | Powered by <b>ShopBolt</b></p>
        </div>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
