<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';
if (!isLoggedIn() || $_SESSION['role'] !== 'ADMIN') {
    header("Location: index.php");
    exit;
}

$db = getDB();

if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute([':id' => $postId]);
    header("Location: admin_dashboard.php");
    exit;
}

?>

<h1>Admin Dashboard</h1>
<p>Welcome, Admin! You can manage all users posts here.</p>

<button onclick="window.history.back();">Back</button>

<?php
$posts = getPosts();
foreach ($posts as $post) {
    echo "<div>";
    echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
    echo "<p>" . htmlspecialchars($post['content']) . "</p>";
    echo "<a href='admin_dashboard.php?id=" . $post['id'] . "'>Delete Post</a>";
    echo "</div>";
}
?>
