function showSection(id) {
    document.querySelectorAll("main section").forEach(section => {
      section.classList.add("hidden");
    });
    document.getElementById(id).classList.remove("hidden");
  }
  
  function logout() {
    alert("You have logged out!");
  }
  
  document.getElementById("productForm").addEventListener("submit", function (e) {
    e.preventDefault();
  
    const name = document.getElementById("name").value;
    const description = document.getElementById("description").value;
    const price = document.getElementById("price").value;
    const category = document.getElementById("category").value;
    const stock = document.getElementById("stock").value;
  
    const id = Math.floor(Math.random() * 10000);
    const tbody = document.getElementById("productTableBody");
    const row = document.createElement("tr");
  
    row.setAttribute("data-description", description);
    row.innerHTML = `
      <td>${id}</td>
      <td>${name}</td>
      <td>${price}</td>
      <td>${category}</td>
      <td>${stock}</td>
      <td>
        <button onclick="editProduct(this)">Edit</button>
        <button onclick="deleteProduct(this)">Delete</button>
      </td>
    `;
  
    tbody.appendChild(row);
    this.reset();
  });
  
  function deleteProduct(button) {
    button.closest("tr").remove();
  }
  
  function editProduct(button) {
    const row = button.closest("tr");
    const cells = row.querySelectorAll("td");
  
    const name = cells[1].textContent;
    const price = cells[2].textContent;
    const category = cells[3].textContent;
    const stock = cells[4].textContent;
    const description = row.getAttribute("data-description");
  
    document.getElementById("name").value = name;
    document.getElementById("price").value = price;
    document.getElementById("category").value = category;
    document.getElementById("stock").value = stock;
    document.getElementById("description").value = description;
  
    row.remove();
    showSection("create");
  }
  