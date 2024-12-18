<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();
include '../includes/db.php';
include '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        try {
            $db = getDB();

            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $redirectUrl = strtoupper($user['role']) === 'ADMIN' ? 'admin_dashboard.php' : 'user_dashboard.php';
                header("Location: $redirectUrl");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "An error occurred. Please try again later.";
            error_log("Database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
    </nav>
</header>

<main>
    <div class="login-container">
        <h1>Login</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>

        <p style="margin-top: 15px;">Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y"); ?> My Blog</p>
</footer>
</body>
</html>
