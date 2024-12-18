<?php
use PHPUnit\Framework\TestCase;

class CreatePostTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        session_start();

        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
        $this->db->exec("CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT, content TEXT, user_id INTEGER, FOREIGN KEY(user_id) REFERENCES users(id))");
        $this->db->exec("INSERT INTO users (username, password) VALUES ('testuser', '" . password_hash('password', PASSWORD_DEFAULT) . "')");

        $_SESSION = [];
    }

    public function testCreatePostMissingTitle()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $_POST['content'] = 'This is the content of the test post.';
        $_POST['csrf_token'] = $_SESSION['csrf_token'];

        ob_start();
        $result = $this->handleCreatePost();
        ob_end_clean();

        $this->assertFalse($result);
    }

    public function testCreatePostMissingContent()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $_POST['title'] = 'Test Post Title';
        $_POST['csrf_token'] = $_SESSION['csrf_token'];

        ob_start();
        $result = $this->handleCreatePost();
        ob_end_clean();

        $this->assertFalse($result);
    }

    private function handleCreatePost()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }

        if (empty($_SESSION['user_id'])) {
            return false;
        }

        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);

        if (empty($title) || empty($content)) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }
}
?>
