

<?php

use PHPUnit\Framework\TestCase;

class EditPostTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT, content TEXT, user_id INTEGER, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
        $this->db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
        $this->db->exec("INSERT INTO users (id, username, password) VALUES (1, 'testuser', 'testpassword')");
        $this->db->exec("INSERT INTO posts (id, title, content, user_id) VALUES (1, 'Old Title', 'Old Content', 1)");
        $_SESSION = [];
        session_start();
    }

    public function testEditPostSuccess()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['csrf_token'] = 'test_csrf_token';
        $_GET['id'] = 1;
        $_POST = [
            'csrf_token' => 'test_csrf_token',
            'title' => 'Updated Title',
            'content' => 'Updated Content'
        ];

        $postBeforeUpdate = getPostById(2);
        $this->assertEquals('Old Title', $postBeforeUpdate['title']);
        $this->assertEquals('Old Content', $postBeforeUpdate['content']);

        ob_start();
        include __DIR__ . '/../public/edit_post.php';
        $output = ob_get_clean();

        $this->assertStringContainsString("Post updated successfully.", $output);

        $postAfterUpdate = getPostById(1);
        $this->assertEquals('Updated Title', $postAfterUpdate['title']);
        $this->assertEquals('Updated Content', $postAfterUpdate['content']);
    }

    public function testEditPostInvalidCsrfToken()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['csrf_token'] = 'test_csrf_token';
        $_GET['id'] = 1;
        $_POST = [
            'csrf_token' => 'invalid_csrf_token',
            'title' => 'Updated Title',
            'content' => 'Updated Content'
        ];

        ob_start();
        include __DIR__ . '/../public/edit_post.php';
        $output = ob_get_clean();

        $this->assertStringContainsString("Invalid CSRF token.", $output);
    }

    public function testEditPostNoUserLoggedIn()
    {
        unset($_SESSION['user_id']);
        $_GET['id'] = 1;
        ob_start();
        include __DIR__ . '/../public/edit_post.php';
        $output = ob_get_clean();

        $this->assertStringContainsString("You need to be logged in.", $output);
    }

    protected function tearDown(): void
    {
        session_write_close();
    }
}
?>
