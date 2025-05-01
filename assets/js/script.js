<script>
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const shippingFee = 10.00;
  const validCouponCode = "SHOPBOLT";
  const discountRate = 0.10;
  let isCouponApplied = false;

  // Utility to safely get an element by ID
  const getEl = (id) => document.getElementById(id);

  // Calculate totals and update the DOM
  function calculateTotals(applyDiscount = false) {
    let subtotal = 0;
    cart.forEach(item => {
      const price = parseFloat(item.price.replace('$', '')) || 0;
      subtotal += price * item.quantity;
    });

    const discount = applyDiscount ? subtotal * discountRate : 0;
    const total = subtotal - discount + shippingFee;

    if (getEl("cart-subtotal")) getEl("cart-subtotal").textContent = `$${subtotal.toFixed(2)}`;
    if (getEl("shipping-fee")) getEl("shipping-fee").textContent = `$${shippingFee.toFixed(2)}`;
    if (getEl("cart-total")) getEl("cart-total").textContent = `$${total.toFixed(2)}`;

    return { subtotal, discount, total };
  }

  // Apply coupon logic
  const applyCouponBtn = getEl("apply-coupon");
  if (applyCouponBtn) {
    applyCouponBtn.addEventListener("click", () => {
      const code = getEl("coupon-code").value.trim().toUpperCase();
      const messageEl = getEl("coupon-message");

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
  }

  // Initial calculation
  calculateTotals();

  // Handle form submission
  const checkoutForm = getEl("checkout-form");
  if (checkoutForm) {
    checkoutForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const name = getEl("checkout-name").value.trim();
      const email = getEl("checkout-email").value.trim();
      const phone = getEl("checkout-phone").value.trim();
      const city = getEl("checkout-city").value.trim();
      const address = getEl("checkout-address").value.trim();

      if (!name || !email || !phone || !city || !address) {
        alert("Please fill in all the required fields.");
        return;
      }

      const { subtotal, discount, total } = calculateTotals(isCouponApplied);

      const orderData = {
        name,
        email,
        phone,
        city,
        address,
        cart,
        subtotal,
        discount,
        shipping: shippingFee,
        total
      };

      fetch("save_order.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(orderData)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          localStorage.removeItem("cart");
          showThankYouPopup();
        } else {
          alert("Error placing order: " + data.message);
        }
      })
      .catch(err => {
        console.error("Checkout error:", err);
        alert("An error occurred during checkout.");
      });
    });
  }

  // Show thank you popup
  function showThankYouPopup() {
    const popup = document.createElement("div");
    popup.id = "thank-you-popup";
    popup.innerHTML = `
      <div class="popup-content text-center">
        <h3>🎉 Thank you for your order!</h3>
        <p>Redirecting to Home page...</p>
      </div>
    `;
    document.body.appendChild(popup);

    // Inject CSS styles for popup
    const popupStyle = `
      <style>
        #thank-you-popup {
          position: fixed;
          top: 0;
          left: 0;
          width: 100vw;
          height: 100vh;
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
    `;
    document.head.insertAdjacentHTML("beforeend", popupStyle);

    setTimeout(() => {
      window.location.href = "index.php";
    }, 1500);
  }
</script>
.then(response => {
  if (!response.ok) {
    throw new Error(`HTTP error! Status: ${response.status}`);
  }
  return response.json();
})
