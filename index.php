<?php
session_start();
require 'db.php';


$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/easy.css" />
</head>
<body>

  
  <header>
    <nav class="navbar">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="service.php">Services</a>
      <a href="shop.php">Shop</a>
      <a href="contact.php">Contact</a>

      <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
        <a href="login.php">Login</a>
      <?php else: ?>
        <a href="account.php">Account</a>
        <a href="logout.php">Logout</a>
      <?php endif; ?>

      <a href="cart.html">Cart</a>
    </nav>

    <a href="index.php" class="nav_logo">
      <img src="assets/images/logo.png" alt="Logo" />
    </a>
  </header>

  
  <section id="home">
    <div class="container">
      <h5>NEW ARRIVAL</h5>
      <h1><span>Best Prices</span> This Season</h1>
      <p>ShopBolt offers the best products for the most affordable prices</p>
      <a href="shop.php"><button>Shop Now</button></a>
    </div>
  </section>

 
  <section id="brand" class="container">
    <div class="row">
      <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/brand1_sample.jpg" alt="">
      <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/brand2_sample.jpg" alt="">
      <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/brand3_sample.jpg" alt="">
      <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/images/brand4_sample.jpg" alt="">
    </div>
  </section>

 
  <section id="new" class="w-100">
    <div class="row p-0 m-0">
      <div class="one col-lg-4 col-md-12 col-sm-12 p-0">
        <img class="img-fluid" src="assets/images/brand1.jpg" alt="">
        <div class="details">
          <h2>Extremely Awesome Sneakers</h2>
         <a href="shop.php"> <button class="text-uppercase">Shop Now</button></a>
        </div>
      </div>
    </div>
  </section>


  <section id="product1" class="section-p1">
    <h2>Feature Product</h2>
    <p>Our Last Stock, Please Check it out!</p>
    <div class="pro-container">
      <?php while ($row = $result->fetch_assoc()): ?>
      <div class="pro">
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
        <div class="des">
          <span><?= htmlspecialchars($row['title']) ?></span>
          <h5><?= htmlspecialchars($row['description']) ?></h5>
          <h4>$<?= number_format($row['price'], 2) ?></h4>
        </div>
        <a href="#"><i class="bi bi-cart cart"></i></a>
      </div>
      <?php endwhile; ?>
    </div>
  </section>


  <section id="banner" class="section-m1">
    <h4>Rent Service</h4>
    <h2>Up to <span>70% OFF</span> All Electronics & Home Accessories</h2>
    <button class="button_normal">Explore More</button>
  </section>

 
  <section id="product1" class="section-p1" style="background-color:bisque;width: 100%;">
    <h2>New Arrivals</h2>
    <p>Our Latest Stock, Please Check it out!</p>
    <div class="pro-container">
    
    </div>
  </section>


  <footer id="contact">
    <div class="container">
      <div class="footer-hr flex flex-col">
        <div class="flex gap-1">
          <hr>
          <h6>Newsletter</h6>
        </div>
        <h3>Join Our Mailing List</h3>
        <p class="text-center">Stay updated with our latest news and products.</p>
      </div>
      <form action="#" id="footer-form" class="flex flex-sb gap-2">
        <div id="footer-message"></div>
        <input type="email" required placeholder="Enter your email">
        <button type="submit" class="btn_hover1">Get Started</button>
      </form>
    </div>

    <div class="footer-menu">
      <div class="container">
        <div class="flex flex-start footer-center">
          <div class="w-33 mt-2 flex-col gap-2 flex-start">
            <a href="/"><img src="assets/images/logo.png" alt="footer-logo"></a>
            <p class="mt-2">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
            <span class="flex gap-20 mt-15">
              <a href="#"><i class="bi bi-facebook"></i></a>
              <a href="#"><i class="bi bi-instagram"></i></a>
              <a href="#"><i class="bi bi-twitter-x"></i></a>
              <a href="#"><i class="bi bi-linkedin"></i></a>
            </span>
          </div>

          <div class="w-16 mt-1">
            <h4>Quick Links</h4>
            <ul class="flex flex-col gap-20 flex-start">
              <li><a href="#">Home</a></li>
              <li><a href="#">About</a></li>
              <li><a href="#">Portfolio</a></li>
              <li><a href="#">Blogs</a></li>
            </ul>
          </div>

          <div class="w-16 mt-45 flex-end">
            <ul class="flex flex-col gap-20 flex-start">
              <li><a href="#">FAQ</a></li>
              <li><a href="#">Contact</a></li>
              <li><a href="#">Team</a></li>
              <li><a href="#">Privacy Policy</a></li>
            </ul>
          </div>

          <div class="w-33 mt-1 flex flex-col flex-start">
            <h4>Get Connected</h4>
            <ul class="flex flex-col gap-2 flex-start">
              <li>
                <a href="#"><i class="bi bi-envelope"></i></a>
                <a href="mailto:shopbolt@gmail.com" class="text-lowercase">shopbolt@gmail.com</a>
              </li>
              <li>
                <a href="#"><i class="bi bi-telephone"></i></a>
                <a href="#">+8801100000000</a>
              </li>
              <li>
                <a href="#"><i class="bi bi-clock"></i></a>
                <a href="#">Office Hours: 8AM - 11PM, Sunday - Weekend</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="copyright">
      <div class="container flex flex-sb gap-20 flex-warp">
        <h6>&copy; 2024 Coded by <a href="/" class="p-0">NoobCoders</a></h6>
        <h6>Powered By <b>Prisom</b></h6>
      </div>
    </div>
  </footer
