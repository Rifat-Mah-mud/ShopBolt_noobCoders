<?php
ini_set('session.cookie_lifetime', 0);
session_start();

include("db_connect/db_connect.php");

$error = "";
$timeout_duration = 600;


if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit;
    } else {
        $_SESSION['last_activity'] = time();
        
        if ($_SESSION['user_role'] === 'admin') {
            header("Location: admin.php");
            exit;
        } else {
            header("Location: index.php");
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, user_role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['last_activity'] = time();

                header("Location: " . ($user['user_role'] === 'admin' ? 'admin.php' : 'account.php'));
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with that email.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - MyStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        header { background-color: #333; padding: 10px 0; }
        .navbar { display: flex; justify-content: center; gap: 20px; }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
        }
        .navbar a:hover { background-color: #555; border-radius: 4px; }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: #f9f9f9;
        }
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
        <a href="admin.php">Admin</a>
    </nav>
</header>


<div class="container login-container">
    <h3 class="text-center mb-4">Login</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($_GET['timeout'])): ?>
        <div class="alert alert-warning">Session expired. Please log in again.</div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Log In</button>
        </div>

        <p class="mt-3 text-center">
            <a href="forgot_password.php">Forgot your password?</a>
        </p>

        <p class="mt-2 text-center">
            Don't have an account? <a href="register.php">Register</a>
        </p>
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
