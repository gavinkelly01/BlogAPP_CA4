<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

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
}

echo "Post not found.";
exit;
