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

if (isset($_GET['id'])) {
    $post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $user_id = $_SESSION['user_id'];

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $post_id, ':user_id' => $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Invalid CSRF token.");
            }

            $stmt = $db->prepare("DELETE FROM posts WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $post_id, ':user_id' => $user_id]);
            header("Location: user_dashboard.php");
            exit;
        }
    } else {
        echo "Post not found or you don't have permission to delete this post.";
        exit;
    }
} else {
    echo "Post ID not specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Post</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self';">
    <meta name="referrer" content="strict-origin">
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
    <div class="confirmation-box">
        <h1>Delete Post</h1>
        <p>Are you sure you want to delete this post: <strong><?= htmlspecialchars($post['title']); ?></strong>?</p>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="confirm-button">Yes, Delete</button>
            <a href="user_dashboard.php" class="cancel-button">Cancel</a>
        </form>
    </div>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Blog</p>
</footer>
</body>
</html>
