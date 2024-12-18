<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();
include '../includes/db.php';


// Registration logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    $error = '';

    if (!$username || !$password || !$confirmPassword) {
        $error = "All fields are required.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        try {
            $db = getDB();

            $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);

            if ($stmt->fetch()) {
                $error = "Username is already taken.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'USER')");
                $stmt->execute([':username' => $username, ':password' => $hashedPassword]);
                header("Location: login.php");
                exit;
            }
        } catch (PDOException $e) {
            error_log("Database error during registration: " . $e->getMessage());
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login or Register</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
    </nav>
</header>

<main>
    <div class="form-container">
        <h1>Register</h1>
        <form method="POST" action="">
            <input type="hidden" name="action" value="register">
            <?php if (!empty($error) && $_POST['action'] === 'register'): ?>
                <p class="error-message"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <label for="register-username">Username:</label>
            <input type="text" id="register-username" name="username" required>

            <label for="register-password">Password:</label>
            <input type="password" id="register-password" name="password" required>

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm_password" required>

            <button type="submit">Register</button>
        </form>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y"); ?> My Blog</p>
</footer>
</body>
</html>
