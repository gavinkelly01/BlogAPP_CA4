<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
]);
session_start();

if (ini_get("session.use_only_cookies") == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}

include '../includes/db.php';
include '../includes/functions.php';

session_regenerate_id(true);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

    if (!$title || !$content) {
        $error_message = "Both title and content are required.";
    } else {
        $user_id = $_SESSION['user_id'];
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            header("Location: user_dashboard.php");
            exit;
        } else {
            $error_message = "Failed to create post. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self';">
    <meta name="referrer" content="strict-origin">
    <title>Create Post</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
        <a href="user_dashboard.php">Dashboard</a>
    </nav>
</header>

<main>
    <h1 style="text-align: center;">Create a New Post</h1>
    <form method="POST">
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" placeholder="Enter post title" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="5" placeholder="Enter post content" required></textarea>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Create Post</button>
    </form>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Blog</p>
</footer>
</body>
</html>
