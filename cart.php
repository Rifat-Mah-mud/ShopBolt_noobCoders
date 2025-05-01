<?php
session_start();
include('db_connect/db_connect.php'); 


$user_id = $_SESSION['logged_in'] === true ? $_SESSION['user_id'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Cart</title>


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <style>
    body { background-color: #f8f9fa; }
    .navbar a { margin-right: 15px; text-decoration: none; color: #000; }
    .cart-img { width: 80px; height: 80px; object-fit: cover; }
    .qty-input { width: 60px; }
  </style>
</head>
<body>

<header>
  <nav class="navbar navbar-expand-lg navbar-light bg-white px-4 py-3 shadow-sm">
    <a class="navbar-brand" href="index.php"><img src="assets/images/logo.png" height="40" alt="Logo"></a>
    <div class="ms-auto">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="shop.php">Shop</a>
      <a href="contact.php">Contact</a>
      <?php if ($user_id): ?>
        <a href="account.php">Account</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Sign In</a>
      <?php endif; ?>
      <a href="cart.php" class="fw-bold text-primary">Cart</a>
    </div>
  </nav>
</header>

<section class="container my-5">
  <h2 class="mb-4">🛒 Your Shopping Cart</h2>
  <div id="cart-container" class="table-responsive"></div>

  <div class="row mt-4">
    <div class="col-md-6">
      <input type="text" id="coupon-code" class="form-control" placeholder="Enter coupon code">
      <button id="apply-coupon" class="btn btn-secondary mt-2">Apply Coupon</button>
      <p id="coupon-message" class="mt-1"></p>
    </div>
    <div class="col-md-6 text-end">
      <p>Subtotal: <span id="cart-subtotal">$0.00</span></p>
      <p>Shipping: <span id="shipping-fee">$0.00</span></p>
      <h4>Total: <span id="cart-total" class="text-success">$0.00</span></h4>
      <button class="btn btn-primary mt-3" id="checkout-button">Proceed to Checkout</button>
    </div>
  </div>
</section>

<footer class="bg-dark text-white text-center py-3 mt-5">
  <p>&copy; 2025 Shopbolt | Designed by NoobCoders</p>
</footer>

<script>
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const shippingFee = 10.00;
  const validCouponCode = "SHOPBOLT";
  const discountRate = 0.10;
  let isCouponApplied = false;

  function calculateTotals(applyDiscount = false) {
    let subtotal = 0;
    cart.forEach(item => {
      const price = parseFloat(item.price.toString().replace(/[^\d.]/g, '')) || 0;
      subtotal += price * item.quantity;
    });

    const discount = applyDiscount ? subtotal * discountRate : 0;
    const total = subtotal - discount + shippingFee;

    document.getElementById("cart-subtotal").textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById("shipping-fee").textContent = `$${shippingFee.toFixed(2)}`;
    document.getElementById("cart-total").textContent = `$${total.toFixed(2)}`;

    return { subtotal, discount, total };
  }

  function renderCart() {
    const container = document.getElementById("cart-container");
    if (cart.length === 0) {
      container.innerHTML = "<p>Your cart is empty.</p>";
      calculateTotals(false);
      return;
    }

    let html = `
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Image</th><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Remove</th>
          </tr>
        </thead><tbody>
    `;

    cart.forEach((item, index) => {
      const price = parseFloat(item.price.toString().replace(/[^\d.]/g, ''));
      const subtotal = price * item.quantity;

      html += `
        <tr>
          <td><img src="${item.img}" class="cart-img" alt="${item.title}"></td>
          <td>${item.title}</td>
          <td>${item.price}</td>
          <td><input type="number" min="1" value="${item.quantity}" class="form-control qty-input" data-index="${index}"></td>
          <td>$${subtotal.toFixed(2)}</td>
          <td><button class="btn btn-sm btn-danger" onclick="removeItem(${index})"><i class="bi bi-trash"></i></button></td>
        </tr>
      `;
    });

    html += "</tbody></table>";
    container.innerHTML = html;
    calculateTotals(isCouponApplied);

    
    document.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('change', (e) => {
        const index = +e.target.getAttribute('data-index');
        const newQty = +e.target.value;
        if (newQty >= 1) {
          cart[index].quantity = newQty;
          localStorage.setItem("cart", JSON.stringify(cart));
          renderCart();
        } else {
          alert("Quantity must be at least 1");
        }
      });
    });
  }

  function removeItem(index) {
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart();
  }

  document.getElementById("apply-coupon").addEventListener("click", () => {
    const code = document.getElementById("coupon-code").value.trim().toUpperCase();
    const msg = document.getElementById("coupon-message");

    if (code === validCouponCode) {
      isCouponApplied = true;
      msg.textContent = "Coupon applied! You saved 10%.";
      msg.className = "text-success";
    } else {
      isCouponApplied = false;
      msg.textContent = "Invalid coupon code.";
      msg.className = "text-danger";
    }

    calculateTotals(isCouponApplied);
  });

  document.getElementById("checkout-button").addEventListener("click", function() {
    if (cart.length === 0) {
      alert("Your cart is empty.");
      return;
    }

    const { subtotal, discount, total } = calculateTotals(isCouponApplied);

    <?php if ($user_id): ?>
      fetch("save_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          cart: cart,
          subtotal: subtotal,
          discount: discount,
          shipping: shippingFee,
          total: total
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          localStorage.removeItem("cart");
          alert("Order placed successfully!");
          window.location.href = "order_confirmation.php";
        } else {
          alert("Failed to place order: " + data.message);
        }
      })
      .catch(err => {
        console.error("Order error:", err);
        alert("Error placing order.");
      });
    <?php else: ?>
      alert("Please sign in to place an order.");
      window.location.href = "login.php";
    <?php endif; ?>
  });

  window.onload = function() {
    console.log("Cart loaded:", cart); // Debugging
    renderCart();
  };
</script>

</body>
</html>
