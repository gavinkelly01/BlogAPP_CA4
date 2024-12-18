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

if (!$post) {
    echo "Post not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $db->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':title' => $_POST['title'], ':content' => $_POST['content'], ':id' => $post_id, ':user_id' => $user_id]);
    header("Location: user_dashboard.php");
    exit;
}
?>

<h2>Edit Post</h2>

<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?php echo $post['title']; ?>" required><br>
    <label>Content:</label><br>
    <textarea name="content" required><?php echo $post['content']; ?></textarea><br>
    <button type="submit">Update Post</button>
</form>



