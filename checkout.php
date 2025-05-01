<?php
session_start();
include("db_connect/db_connect.php");

$user_id = $_SESSION['user_id'] ?? null;
if ($user_id === null) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Checkout</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
<div class="container py-5">
  <h2>Checkout</h2>
  <form id="checkout-form">
    <div class="row">
      <div class="col-md-6">
        <input type="text" id="checkout-name" class="form-control mb-2" placeholder="Full Name" required>
        <input type="email" id="checkout-email" class="form-control mb-2" placeholder="Email" required>
        <input type="tel" id="checkout-phone" class="form-control mb-2" placeholder="Phone" required>
        <input type="text" id="checkout-city" class="form-control mb-2" placeholder="City" required>
        <input type="text" id="checkout-address" class="form-control mb-2" placeholder="Address" required>
      </div>
      <div class="col-md-6">
        <input type="text" id="coupon-code" class="form-control mb-2" placeholder="Coupon Code">
        <button type="button" id="apply-coupon" class="btn btn-secondary mb-2">Apply Coupon</button>
        <p id="coupon-message" class="mb-3"></p>

        <p>Subtotal: <span id="cart-subtotal">$0.00</span></p>
        <p>Shipping: <span id="shipping-fee">$0.00</span></p>
        <h4>Total: <span id="cart-total" class="text-success">$0.00</span></h4>
        <button type="submit" class="btn btn-primary mt-3">Place Order</button>
      </div>
    </div>
  </form>
</div>


<div id="thank-you-popup" style="display:none;">
  <div class="popup-content text-center">
    <h3>🎉 Thank you for your order!</h3>
    <p>Redirecting to Home page...</p>
  </div>
</div>

<style>
  #thank-you-popup {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }
  .popup-content {
    background: white;
    padding: 40px 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  }
</style>

<script>
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const shippingFee = 10.00;
  const validCouponCode = "SHOPBOLT";
  const discountRate = 0.10;
  let isCouponApplied = false;

  function calculateTotals(applyDiscount = false) {
    let subtotal = 0;
    cart.forEach(item => {
      const price = parseFloat(item.price.replace('$', '')) || 0;
      subtotal += price * item.quantity;
    });

    const discount = applyDiscount ? subtotal * discountRate : 0;
    const total = subtotal - discount + shippingFee;

    document.getElementById("cart-subtotal").textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById("shipping-fee").textContent = `$${shippingFee.toFixed(2)}`;
    document.getElementById("cart-total").textContent = `$${total.toFixed(2)}`;

    return { subtotal, discount, total };
  }

  document.getElementById("apply-coupon").addEventListener("click", () => {
    const code = document.getElementById("coupon-code").value.trim().toUpperCase();
    const messageEl = document.getElementById("coupon-message");

    if (code === validCouponCode) {
      isCouponApplied = true;
      calculateTotals(true);
      messageEl.textContent = "Coupon applied! You saved 10%.";
      messageEl.className = "text-success";
    } else {
      isCouponApplied = false;
      calculateTotals(false);
      messageEl.textContent = "Invalid coupon code.";
      messageEl.className = "text-danger";
    }
  });

  calculateTotals();

  document.getElementById("checkout-form").addEventListener("submit", function(e) {
    e.preventDefault();

    const { subtotal, discount, total } = calculateTotals(isCouponApplied);

    const orderData = {
      name: document.getElementById("checkout-name").value,
      email: document.getElementById("checkout-email").value,
      phone: document.getElementById("checkout-phone").value,
      city: document.getElementById("checkout-city").value,
      address: document.getElementById("checkout-address").value,
      cart: cart,
      subtotal: subtotal,
      discount: discount,
      shipping: shippingFee,
      total: total
    };

    fetch("save_order.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(orderData)
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      if (data.success) {
        localStorage.removeItem("cart");
        document.getElementById("thank-you-popup").style.display = "flex";
        setTimeout(() => window.location.href = "index.php", 2000);
      } else {
        alert("Failed to place order: " + (data.message || "Unknown error"));
      }
    })
    .catch(err => {
      alert("Checkout error: " + err.message);
    });
  });
</script>
</body>
</html>
