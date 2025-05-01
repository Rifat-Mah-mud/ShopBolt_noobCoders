<?php
session_start();
include("db_connect/db_connect.php");

$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $renter_name = trim($_POST['renter_name']);
    $renter_email = trim($_POST['renter_email']);
    $rental_duration = intval($_POST['rental_duration']);
    $notes = trim($_POST['notes']);

    if (empty($renter_name) || empty($renter_email) || empty($rental_duration)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO rental_requests (product_id, renter_name, renter_email, rental_duration, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $product_id, $renter_name, $renter_email, $rental_duration, $notes);
        if ($stmt->execute()) {
            $success = "Rental request submitted successfully!";
            header("Location: login.php?message=Rental+request+submitted+successfully");
            exit;
        } else {
            $error = "Failed to submit request. Try again.";
        }
    }
}


$product_result = $conn->query("SELECT id, title FROM products WHERE stock > 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request a Rental - MyStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .rental-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        header { background-color: #333; padding: 10px 0; }
        .navbar { display: flex; justify-content: center; gap: 20px; }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
        }
        .navbar a:hover { background-color: #555; border-radius: 4px; }
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
    </nav>
</header>

<div class="container rental-container">
    <h3 class="text-center mb-4">Request to Rent a Product</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Choose Product:</label>
            <select name="product_id" class="form-control" required>
                <option value="">Select a product</option>
                <?php while ($row = $product_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Your Name:</label>
            <input type="text" name="renter_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Your Email:</label>
            <input type="email" name="renter_email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Rental Duration (days):</label>
            <input type="number" name="rental_duration" class="form-control" required min="1">
        </div>
        <div class="mb-3">
            <label>Additional Notes (optional):</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Submit Request</button>
        </div>
    </form>
</div>

<footer class="bg-light text-center text-lg-start mt-5">
    <div class="text-center p-3" style="background-color: #e9ecef;">
        © <?php echo date("Y"); ?> MyStore. All rights reserved.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
