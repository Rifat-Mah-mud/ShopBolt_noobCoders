document.addEventListener("DOMContentLoaded", function () {
    const loginframe = document.querySelector(".loginframe");
    const registerLink = document.querySelector(".registerlink");
    const loginLink = document.querySelector(".loginlink");

    registerLink.addEventListener("click", (e) => {
        e.preventDefault();
        loginframe.classList.add("active");
    });

    loginLink.addEventListener("click", (e) => {
        e.preventDefault();
        loginframe.classList.remove("active");
    });
});
document.addEventListener("DOMContentLoaded", function () {
    // Add to Cart Functionality
    const addToCartButtons = document.querySelectorAll(".add-to-cart");

    addToCartButtons.forEach(button => {
        button.addEventListener("click", () => {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const price = parseFloat(button.dataset.price);
            const img = button.dataset.img;

            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            const existing = cart.find(item => item.id === id);

            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({ id, name, price, img, quantity: 1 });
            }

            localStorage.setItem("cart", JSON.stringify(cart));
            alert(`${name} added to cart!`);
        });
    });

    // Show & Manage Cart
    if (window.location.pathname.includes("cart.html")) {
        loadCart();
    }

    function loadCart() {
        const cartTableBody = document.querySelector("#cart-table tbody");
        const totalPriceElement = document.getElementById("total-price");
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cartTableBody.innerHTML = "";
        let total = 0;

        cart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            total += subtotal;

            const row = document.createElement("tr");
            row.innerHTML = `
                <td>
                    <img src="${item.img}" width="50" class="me-2">
                    ${item.name}
                </td>
                <td>
                    <input type="number" value="${item.quantity}" min="1" class="form-control quantity-input" data-index="${index}">
                </td>
                <td>$<span class="item-subtotal">${subtotal.toFixed(2)}</span></td>
                <td>
                    <button class="btn btn-danger btn-sm remove-btn" data-index="${index}">Remove</button>
                </td>
            `;
            cartTableBody.appendChild(row);
        });

        totalPriceElement.textContent = `Total: $${total.toFixed(2)}`;

        // Hook into quantity changes
        const qtyInputs = document.querySelectorAll(".quantity-input");
        qtyInputs.forEach(input => {
            input.addEventListener("change", (e) => {
                const index = e.target.dataset.index;
                const newQty = parseInt(e.target.value);
                if (newQty < 1) return;
                cart[index].quantity = newQty;
                localStorage.setItem("cart", JSON.stringify(cart));
                loadCart(); // refresh cart
            });
        });

        // Hook into remove buttons
        const removeBtns = document.querySelectorAll(".remove-btn");
        removeBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                const index = btn.dataset.index;
                cart.splice(index, 1);
                localStorage.setItem("cart", JSON.stringify(cart));
                loadCart(); // refresh cart
            });
        });
    }
});
