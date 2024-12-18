<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';
include '../includes/header.php';
$posts = getPosts();

if ($posts) {
    foreach ($posts as $post) {
        echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";
        $username = isset($post['username']) ? htmlspecialchars($post['username']) : 'Anonymous';
        echo "<small>By " . $username . "</small><br>";
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
            echo "<a href='edit_post.php?id=" . $post['id'] . "'>Edit</a><br>";
        }
    }
} else {
    echo "<p>No posts found.</p>";
}

include_once '../includes/footer.php';
?>
