<?php
session_start();
require 'db.php';


if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
  header('Location: login.php');
  exit;
}


if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
} else {
  
    header('Location: admin.php');
    exit;
}


$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();


if (!$product) {
    header('Location: admin.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
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
            $stmt->bind_param("ssdsdii", $title, $desc, $price, $image, $category, $stock, $product_id);
            $stmt->execute();
            $stmt->close();
            $success_message = "Product updated successfully!";
        } else {
            $error_message = "Image upload failed.";
        }
    } else {
      
        $stmt = $conn->prepare("UPDATE products SET title = ?, description = ?, price = ?, category = ?, stock = ? WHERE id = ?");
        $stmt->bind_param("ssdsdi", $title, $desc, $price, $category, $stock, $product_id);
        $stmt->execute();
        $stmt->close();
        $success_message = "Product updated successfully!";
    }
}


$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Product</title>
  <link rel="stylesheet" href="admin.css" />
  <style>
    label { display: block; margin: 5px 0; }
    input, textarea { width: 100%; padding: 8px; margin-bottom: 10px; }
    button { padding: 8px 16px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
    button:hover { background-color: #45a049; }
    .alert { padding: 10px; margin: 10px 0; }
    .alert-success { background-color: #d4edda; color: #155724; }
    .alert-danger { background-color: #f8d7da; color: #721c24; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Product</h2>

    <?php if (isset($success_message)): ?>
      <div class="alert alert-success"><?= $success_message ?></div>
    <?php elseif (isset($error_message)): ?>
      <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <label for="title">Product Name:</label>
      <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required />

      <label for="description">Description:</label>
      <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

      <label for="price">Price:</label>
      <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" step="0.01" required />

      <label for="category">Category:</label>
      <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required />

      <label for="stock">Stock:</label>
      <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required />

      <label for="image">Image (Leave blank to keep current):</label>
      <input type="file" name="image" accept="image/*" />

      <button type="submit" name="update">Update Product</button>
    </form>

    <p><a href="admin.php">Back to Product Management</a></p>
  </div>
</body>
</html>
