<?php
session_start();
header('Content-Type: application/json'); 
include("db_connect/db_connect.php");

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$cart = $input['cart'] ?? [];
$subtotal = $input['subtotal'] ?? 0;
$discount = $input['discount'] ?? 0;
$shipping = $input['shipping'] ?? 0;
$total = $input['total'] ?? 0;


if (empty($cart)) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}


$stmt = $conn->prepare("INSERT INTO orders (user_id, subtotal, discount, shipping, total) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("idddd", $user_id, $subtotal, $discount, $shipping, $total);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to place order: " . $stmt->error]);
    exit;
}

$order_id = $stmt->insert_id;
$stmt->close();


foreach ($cart as $item) {
    $product_id = $item['id'];
    $quantity = $item['quantity'];
    $price = floatval(str_replace('$', '', $item['price']));

    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_item->bind_param("iiid", $order_id, $product_id, $quantity, $price);
    
    if (!$stmt_item->execute()) {
        echo json_encode(["success" => false, "message" => "Failed to insert order item: " . $stmt_item->error]);
        exit;
    }
    $stmt_item->close();
}

echo json_encode(["success" => true, "message" => "Order placed successfully"]);
?>
