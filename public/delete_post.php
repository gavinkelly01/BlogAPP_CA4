<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $post_id, ':user_id' => $user_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        $stmt = $db->prepare("DELETE FROM posts WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $post_id, ':user_id' => $user_id]);
        header("Location: user_dashboard.php");
        exit;
    } else {
        echo "Post not found or you don't have the sufficient permission to delete this post.";
        exit;
    }
} else {
    echo "Post ID not found.";
    exit;
}
