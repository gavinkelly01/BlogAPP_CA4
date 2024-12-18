<?php
session_set_cookie_params([
    'samesite' => 'Strict' ,
    'secure' => true,
    'httponly' => true

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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #eaf7ff;
            color: #333;
            line-height: 1.6;
        }

        h1, h2, h3 {
            font-weight: bold;
            color: #333;
        }

        header {
            background-color: #4fa3f7;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }

        header nav {
            display: inline-block;
        }

        header nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        header nav a:hover {
            color: #c6e5ff;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .login-container {
            background-color: white;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }

        .login-container h1 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #333;
        }

        .login-container form label {
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
            text-align: left;
        }

        .login-container form input[type="text"],
        .login-container form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container form button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4fa3f7;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container form button:hover {
            background-color: #2e86d6;
        }

        .error {
            color: #d9534f;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }

        footer {
            background-color: #4fa3f7;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 50px;
        }

        @media screen and (max-width: 768px) {
            header nav a {
                margin: 0 10px;
            }

            .login-container {
                width: 90%;
            }
        }

        @media screen and (max-width: 480px) {
            header {
                padding: 10px;
            }

            .login-container {
                width: 100%;
                padding: 15px;
            }
        }
    </style>
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
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" >
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" >
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
