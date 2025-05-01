<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php'; 
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shopbolt/Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/shop.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card img {
      height: 200px;
      object-fit: cover;
    }
  </style>
</head>
<body>


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


<section id="product1" class="section-p1 container my-5">
  <h2 class="mb-4">Featured Products</h2>
  <div id="product-container" class="row g-4">

    <?php while ($row = $products->fetch_assoc()): ?>
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
            <h6 class="text-success">$<?= htmlspecialchars($row['price']) ?></h6>
          </div>
          <div class="card-footer text-center bg-white">
            <button class="btn btn-outline-primary w-100 add-to-cart"
                    data-id="<?= $row['id'] ?>"
                    data-title="<?= htmlspecialchars($row['title']) ?>"
                    data-price="<?= htmlspecialchars($row['price']) ?>"
                    data-image="uploads/<?= htmlspecialchars($row['image']) ?>">
              <i class="bi bi-cart"></i> Add to Cart
            </button>
          </div>
        </div>
      </div>
    <?php endwhile; ?>

  </div>
</section>


<footer id="contact">
  
</footer>

<script>
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function () {
      const product = {
        id: this.dataset.id,
        title: this.dataset.title,
        price: parseFloat(this.dataset.price),
        image: this.dataset.image,
        quantity: 1
      };

      let cart = JSON.parse(localStorage.getItem('cart')) || [];

      const existing = cart.find(item => item.id === product.id);
      if (existing) {
        existing.quantity += 1;
      } else {
        cart.push(product);
      }

      localStorage.setItem('cart', JSON.stringify(cart));
      alert(`${product.title} added to cart!`);
    });
  });
</script>

</body>
</html>
