<?php
function isLoggedIn() {
    return session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user_id']);
}

function getPosts() {
    try {
        $db = getDB();
        $query = "
            SELECT 
                posts.id, 
                posts.title, 
                posts.content, 
                posts.created_at, 
                users.username 
            FROM posts 
            LEFT JOIN users ON posts.user_id = users.id 
            ORDER BY posts.created_at DESC
        ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log("Database error in getPosts(): " . $e->getMessage());
        return [];
    }
}

function getPostById($postId) {
    try {
        $postId = filter_var($postId, FILTER_VALIDATE_INT);
        if (!$postId) {
            throw new InvalidArgumentException("Invalid post ID provided.");
        }

        $db = getDB();
        $query = "
            SELECT 
                id, 
                title, 
                content, 
                created_at, 
                user_id 
            FROM posts 
            WHERE id = :id
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (InvalidArgumentException $e) {
        error_log("Validation error in getPostById(): " . $e->getMessage());
        return null;
    } catch (PDOException $e) {
        error_log("Database error in getPostById(): " . $e->getMessage());
        return null;
    }
}
?>
