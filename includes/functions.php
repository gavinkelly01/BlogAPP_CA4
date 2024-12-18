<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getPosts() {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT posts.*, users.username FROM posts LEFT JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $posts ? $posts : [];
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

function getPostById($postId) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->execute([':id' => $postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        return $post ? $post : null;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return null;
    }
}
?>
