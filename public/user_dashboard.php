<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

session_regenerate_id(true);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['username']) || empty($_SESSION['user_id'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$db = getDB();
$stmt = $db->prepare("SELECT id, title, content, created_at FROM posts WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self';">
    <meta name="referrer" content="strict-origin">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
        <a href="create_post.php">Create New Post</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="dashboard-container">
    <h1>User Dashboard</h1>

    <?php if ($posts): ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h2><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?= nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                <div class="actions">
                    <a href="edit_post.php?id=<?= urlencode($post['id']); ?>&csrf_token=<?= $csrf_token; ?>">Edit</a>
                    <a href="delete_post.php?id=<?= urlencode($post['id']); ?>&csrf_token=<?= $csrf_token; ?>"
                       onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-posts">You have no posts yet. <a href="create_post.php" class="create-post-btn">Create a new post</a></p>
    <?php endif; ?>

</main>

<footer>
    <p>&copy; <?= date("Y") ?> My Blog</p>
</footer>
</body>
</html>
