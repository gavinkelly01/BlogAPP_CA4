<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];  
    $content = $_POST['content']; 
    $user_id = $_SESSION['user_id'];  
    $db = getDB();
    $query = "SELECT * FROM posts WHERE title = '$title' AND content = '$content'";
    $result = $db->query($query);

    header("Location: user_dashboard.php");
    exit;
}
?>

<h2>Create a New Post</h2>

<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" ><br>
    <label>Content:</label><br>
    <textarea name="content" ></textarea><br>
    <button type="submit">Create Post</button>
</form>
