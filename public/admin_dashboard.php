<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['csrf_token'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: index.php");
    exit;
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $postId = (int)$_POST['id'];
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->bindParam(':id', $postId, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

$posts = getPosts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="dashboard-container">
    <h1>Admin Dashboard</h1>
    <p style="text-align: center;">Manage all posts from users.</p>

    <?php if ($posts): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h2><?= htmlspecialchars($post['title']); ?></h2>
                <p><?= nl2br(htmlspecialchars($post['content'])); ?></p>
                <p class="author">By <?= htmlspecialchars($post['username'] ?? 'Anonymous'); ?></p>
                <div class="actions">
                    <form action="admin_dashboard.php" method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="id" value="<?= $post['id']; ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this post?');">Delete Post</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; color: #555;">No posts available.</p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Blog</p>
</footer>
</body>
</html>
