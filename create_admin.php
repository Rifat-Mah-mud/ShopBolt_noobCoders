<?php
require 'db.php';


$username = "Admin";
$email = "admin123@gmail.com";
$password = "123456";
$user_role = "admin";


$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Admin user already exists.";
    $check->close();
    $conn->close();
    exit;
}
$check->close();


$hashed_password = password_hash($password, PASSWORD_DEFAULT);


$insert = $conn->prepare("INSERT INTO users (username, email, password, user_role) VALUES (?, ?, ?, ?)");
if (!$insert) {
    die("Prepare failed: " . $conn->error);
}

$insert->bind_param("ssss", $username, $email, $hashed_password, $user_role);

if ($insert->execute()) {
    echo "✅ Admin user created successfully.";
} else {
    echo "❌ Error: " . $insert->error;
}

$insert->close();
$conn->close();
?>
