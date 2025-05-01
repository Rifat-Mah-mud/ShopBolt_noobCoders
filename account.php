<?php
session_start();
include("db_connect/db_connect.php");

// Session timeout logic
$timeout_duration = 600;
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

$username = $_SESSION['username'] ?? "Guest";
$email = $_SESSION['email'] ?? "Not available";
$user_id = $_SESSION['user_id'] ?? 0;

// Logout logic
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Fetch orders
$orders = [];
if ($user_id) {
    $stmt = $conn->prepare("
        SELECT o.id AS order_id, o.order_date, o.subtotal, o.discount, o.shipping, o.total, 
               oi.quantity, oi.price, p.title AS product_name
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New password and confirmation do not match.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && password_verify($current_password, $row['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
            $stmt->close();
            $success_message = "Password changed successfully.";
        } else {
            $error_message = "Current password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="shop.php">Shop</a>
        <a href="contact.php">Contact</a>
        <a href="account.php">Account</a>
        <a href="cart.php">Cart</a>
    </nav>
    <a href="index.php" class="nav_logo">
        <img src="assets/images/logo.png" alt="Logo">
    </a>
</header>

<section class="container my-5 py-5">
    <div class="row">
        <!-- Account Info -->
        <div class="col-lg-6">
            <h3>Account Info</h3>
            <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><a href="account.php?logout=1" class="btn btn-outline-danger">Logout</a></p>
        </div>

        <!-- Order History -->
        <div class="col-lg-6">
            <h3>Your Orders</h3>
            <?php if (count($orders) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                <td><?= htmlspecialchars($order['order_date']) ?></td>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td><?= htmlspecialchars($order['quantity']) ?></td>
                                <td>$<?= number_format($order['price'], 2) ?></td>
                                <td>$<?= number_format($order['subtotal'], 2) ?></td>
                                <td>$<?= number_format($order['discount'], 2) ?></td>
                                <td>$<?= number_format($order['total'], 2) ?></td>
                                <td>Completed</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no orders yet.</p>
            <?php endif; ?>
        </div>

        <!-- Rental Requests -->
        <div class="col-lg-12 mt-5">
            <h3>Your Rental Requests</h3>
            <?php
            $rental_requests = [];
            $stmt = $conn->prepare("
                SELECT r.id, p.title AS product_name, r.renter_name, r.renter_email, r.rental_duration, r.notes, r.created_at, r.status
                FROM rental_requests r
                JOIN products p ON r.product_id = p.id
                WHERE r.renter_email = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $rental_requests[] = $row;
            }
            $stmt->close();
            ?>

            <?php if (count($rental_requests) > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Product</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Duration</th>
                            <th>Notes</th>
                            <th>Requested At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rental_requests as $request): ?>
                            <tr>
                                <td><?= htmlspecialchars($request['id']) ?></td>
                                <td><?= htmlspecialchars($request['product_name']) ?></td>
                                <td><?= htmlspecialchars($request['renter_name']) ?></td>
                                <td><?= htmlspecialchars($request['renter_email']) ?></td>
                                <td><?= htmlspecialchars($request['rental_duration']) ?> day(s)</td>
                                <td><?= htmlspecialchars($request['notes']) ?></td>
                                <td><?= htmlspecialchars($request['created_at']) ?></td>
                                <td>
                                    <?php if ($request['status'] === 'Approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php elseif ($request['status'] === 'Rejected'): ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have not made any rental requests yet.</p>
            <?php endif; ?>
        </div>

        <!-- Change Password -->
        <div class="col-lg-6 mt-5">
            <h3>Change Password</h3>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php elseif (isset($success_message)): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php endif; ?>

            <form action="account.php" method="POST">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</section>

<footer class="footer text-center mt-5">
    <p>&copy; 2025 Shopbolt | Designed by NoobCoders</p>
</footer>

</body>
</html>
