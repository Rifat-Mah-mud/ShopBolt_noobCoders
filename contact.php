<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shop</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- Your Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/easy.css">
  <link rel="stylesheet" href="assets/css/checkout.css">
</head>
<body>

<!-- Header with Navigation -->
<header>
  <nav class="navbar">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="shop.php">Shop</a>
    <a href="contact.php">Contact</a>

    <?php
    // Start the session
    session_start();

    // Check if the user is logged in
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        // User is logged in, show the Logout link
        echo '<a href="logout.php">Logout</a>';
    } else {
        // User is not logged in, show the Sign In link
        echo '<a href="login.php">Sign In</a>';
    }
    ?>

    <a href="account.php">Account</a>
    <a href="cart.php">Cart</a>
  </nav>
  <a href="index.php" class="nav_logo">
    <img src="assets/images/logo.png" alt="Logo">
  </a>
</header>


<section class="container my-5 py-5">
  <div class="container text-center mt-5">
    <h3>Contact us</h3>
    <hr class="mx-auto">
    <p class="w-50 mx-auto"><strong>Email:</strong> ShopBolt@gmail.com</p>
    <p class="w-50 mx-auto"><strong>Phone:</strong> 01XXX-XXXXXX</p>
    <p class="w-50 mx-auto"><strong>Address:</strong> Mohammadpur, Dhaka, Bangladesh</p>
  </div>
</section>


<footer>
  <div class="container">
    <div class="footer-hr flex flex-col">
      <div class="flex gap-1">
        <hr>
        <h6>Newsletter</h6>
      </div>
      <h3>Join Our Mailing List</h3>
      <p class="text-center">Your team member's will reach you soon.</p>
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
          <a href="index.php"><img src="assets/images/logo.png" alt="footer-logo"></a>
          <p class="mt-2">Let's have a checkout.</p>
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
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="#">Portfolio</a></li>
            <li><a href="#">Blogs</a></li>
          </ul>
        </div>
        <div class="w-16 mt-45 flex-end">
          <ul class="flex flex-col gap-20 flex-start">
            <li><a href="#">FAQ</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="#">Team</a></li>
            <li><a href="#">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="w-33 mt-1 flex flex-col flex-start">
          <h4>Get Connected</h4>
          <ul class="flex flex-col gap-2 flex-start">
            <li><i class="bi bi-envelope"></i> <a href="#" class="text-lowercase">shopblot@gmail.com</a></li>
            <li><i class="bi bi-telephone"></i> <a href="#">01XXX-XXXXXX</a></li>
            <li><i class="bi bi-clock"></i> <a href="#">Office-Hours: 8AM - 11PM, Sunday - Weekend Day</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="copyright">
    <div class="container flex flex-sb gap-20 flex-wrap">
      <h6>&copy; 2025 Coded by <a href="index.php" class="p-0">NoobCoders</a></h6>
      <h6>Powered By <b>ShopBolt</b></h6>
    </div>
  </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>
