<?php
session_start();
require 'db.php';

// Ensure the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  header('Location: login.php');
  exit;
}

// Handle Product Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
  $title = $_POST['title'];
  $desc = $_POST['description'];
  $price = $_POST['price'];
  $category = $_POST['category'];
  $stock = $_POST['stock'];
  $image = $_FILES['image']['name'];
  $target = "uploads/" . basename($image);

  if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
    $stmt = $conn->prepare("INSERT INTO products (title, description, price, image, category, stock) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssi", $title, $desc, $price, $image, $category, $stock);
    if ($stmt->execute()) {
      $success_message = "Product added successfully!";
    } else {
      $error_message = "Error adding product.";
    }
    $stmt->close();
  } else {
    $error_message = "Image upload failed.";
  }
}

// Handle Product Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $desc = $_POST['description'];
  $price = $_POST['price'];
  $category = $_POST['category'];
  $stock = $_POST['stock'];

  if ($_FILES['image']['name']) {
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
      $stmt = $conn->prepare("UPDATE products SET title = ?, description = ?, price = ?, image = ?, category = ?, stock = ? WHERE id = ?");
      $stmt->bind_param("ssdsdii", $title, $desc, $price, $image, $category, $stock, $id);
      $stmt->execute();
      $stmt->close();
    }
  } else {
    $stmt = $conn->prepare("UPDATE products SET title = ?, description = ?, price = ?, category = ?, stock = ? WHERE id = ?");
    $stmt->bind_param("ssdsdi", $title, $desc, $price, $category, $stock, $id);
    $stmt->execute();
    $stmt->close();
  }
}

// Handle Product Delete
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}

// Handle Rental Request Approve/Reject
if (isset($_POST['action']) && isset($_POST['request_id'])) {
  $request_id = intval($_POST['request_id']);
  $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
  $stmt = $conn->prepare("UPDATE rental_requests SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $status, $request_id);
  $stmt->execute();
  $stmt->close();
}

// Fetch data
$products = $conn->query("SELECT * FROM products");
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$total_stock = $conn->query("SELECT SUM(stock) FROM products")->fetch_row()[0];
$rental_requests = $conn->query("
  SELECT rr.*, p.title AS product_name 
  FROM rental_requests rr 
  JOIN products p ON rr.product_id = p.id 
  ORDER BY rr.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="assets/css/admin.css">
  <style>
    .hidden { display: none; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 10px; text-align: center; }
    img { max-height: 50px; }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Admin Panel</h2>
      <ul>
        <li><button onclick="showSection('dashboard')">Dashboard</button></li>
        <li><button onclick="showSection('create')">Create Product</button></li>
        <li><button onclick="showSection('rental_requests')">Rental Requests</button></li>
        <li><button onclick="showSection('orders')">Orders</button></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Dashboard -->
      <section id="dashboard">
        <h1>Welcome to Admin Dashboard</h1>
        <p>Total Products: <?= $total_products ?></p>
        <p>Total Stock: <?= $total_stock ?></p>
      </section>

      <!-- Create Product -->
      <section id="create" class="hidden">
        <h2>Create New Product</h2>
        <?php if (isset($success_message)): ?>
          <div class="alert alert-success"><?= $success_message ?></div>
        <?php elseif (isset($error_message)): ?>
          <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
          <label>Product Name:</label>
          <input type="text" name="title" required />

          <label>Description:</label>
          <textarea name="description" required></textarea>

          <label>Price:</label>
          <input type="number" name="price" step="0.01" required />

          <label>Category:</label>
          <input type="text" name="category" required />

          <label>Stock:</label>
          <input type="number" name="stock" required />

          <label>Image:</label>
          <input type="file" name="image" accept="image/*" required />

          <button type="submit" name="add">Add Product</button>
        </form>

        <h3>Product List</h3>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Name</th>
              <th>Price</th>
              <th>Category</th>
              <th>Stock</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $products->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt=""></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td>$<?= htmlspecialchars($row['price']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= htmlspecialchars($row['stock']) ?></td>
                <td>
                  <a href="edit_product.php?id=<?= $row['id'] ?>">Edit</a> |
                  <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>

      <!-- Rental Requests -->
      <section id="rental_requests" class="hidden">
        <h2>Rental Requests</h2>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Product</th>
              <th>Name</th>
              <th>Email</th>
              <th>Duration</th>
              <th>Notes</th>
              <th>Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($req = $rental_requests->fetch_assoc()): ?>
              <tr>
                <td><?= $req['id'] ?></td>
                <td><?= htmlspecialchars($req['product_name']) ?></td>
                <td><?= htmlspecialchars($req['renter_name']) ?></td>
                <td><?= htmlspecialchars($req['renter_email']) ?></td>
                <td><?= $req['rental_duration'] ?> day(s)</td>
                <td><?= htmlspecialchars($req['notes']) ?></td>
                <td><?= $req['created_at'] ?></td>
                <td><?= $req['status'] ?? 'Pending' ?></td>
                <td>
                  <?php if (!$req['status']): ?>
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                      <button type="submit" name="action" value="approve">Approve</button>
                      <button type="submit" name="action" value="reject">Reject</button>
                    </form>
                  <?php else: ?>
                    <em><?= ucfirst($req['status']) ?></em>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>

      <!-- Orders -->
      <section id="orders" class="hidden">
        <h2>Orders</h2>
        <p>Coming soon...</p>
      </section>
    </main>
  </div>

  <script>
    function showSection(id) {
      document.querySelectorAll('section').forEach(section => {
        section.classList.add('hidden');
      });
      document.getElementById(id).classList.remove('hidden');
    }
    showSection('dashboard');
  </script>
</body>
</html>
