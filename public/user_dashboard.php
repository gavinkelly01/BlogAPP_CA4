<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$db = getDB();
$stmt = $db->prepare("SELECT * FROM posts WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> 


<a href="index.php">Back to Dashboard</a>
<h2>Your Posts</h2>
<a href="create_post.php">Create a new post</a>

<?php if ($posts): ?>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <strong><?php echo htmlspecialchars($post['title']); ?></strong><br>
                <?php echo htmlspecialchars($post['content']); ?><br>
                <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a> | 
                <a href="delete_post.php?id=<?php echo $post['id']; ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>You have no posts yet.</p>

<?php endif; ?>
