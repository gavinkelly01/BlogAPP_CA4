<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
]);
session_start();
include '../includes/db.php';
include '../includes/functions.php';

session_regenerate_id(true);
$message = "";

if (!isset($_SESSION['user_id'])) {
    $message = "You need to be logged in.";
    echo $message;
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

if (isset($_GET['id'])) {
    $post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];

    if (!$post_id) {
        $message = "Invalid post ID.";
        echo $message;
        exit;
    }

    $db = $db ?? getDB();
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $post_id, ':user_id' => $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        $message = "Post not found or you don't have permission to edit this post.";
        echo $message;
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $message = "Invalid CSRF token.";
            echo $message;
            exit;
        }

        $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $content = trim(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        if (!$title || !$content) {
            $message = "Title and content are required.";
            echo $message;
            exit;
        }

        $stmt = $db->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id AND user_id = :user_id");
        if ($stmt->execute([':title' => $title, ':content' => $content, ':id' => $post_id, ':user_id' => $user_id])) {
            $message = "Post updated successfully.";
        } else {
            $message = "Failed to update post. Please try again.";
        }
        echo $message;
    }
} else {
    $message = "Post ID not specified.";
    echo $message;
}

if (php_sapi_name() != 'cli' || (isset($argv[1]) && $argv[1] === '--test')) {
    echo $message;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self';">
    <meta name="referrer" content="strict-origin">
    <title>Edit Post</title>
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
    <h1 style="text-align: center;">Edit Post</h1>
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="5" required><?= htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Update Post</button>
    </form>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Blog</p>
</footer>
</body>
</html>
