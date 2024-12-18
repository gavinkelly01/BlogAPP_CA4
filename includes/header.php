<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <?php if (isLoggedIn()): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a> | <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
