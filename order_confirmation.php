<?php
session_start();
include('db_connect/db_connect.php');


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];


$statement = $conn->prepare("
    SELECT o.id, o.total, o.created_at 
    FROM orders o 
    WHERE o.user_id = ? 
    ORDER BY o.id DESC 
    LIMIT 1
");
$statement->bind_param("i", $user_id);
$statement->execute();
$result = $statement->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    $order = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Order Confirmation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background-color: #f5f5f5;
      padding-top: 50px;
    }
    .card {
      max-width: 600px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card p-4">
    <?php if ($order): ?>
      <h2 class="text-success">✅ Thank you for your order!</h2>
      <p>Order ID: <strong>#<?= htmlspecialchars($order['id']) ?></strong></p>
      <p>Total: <strong>$<?= number_format($order['total'], 2) ?></strong></p>
      <p>Order Date: <strong><?= $order['created_at'] ?></strong></p>
      <a href="shop.php" class="btn btn-primary mt-3">Continue Shopping</a>
    <?php else: ?>
      <h4 class="text-danger">No recent orders found.</h4>
      <a href="shop.php" class="btn btn-secondary mt-3">Back to Shop</a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
