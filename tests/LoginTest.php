<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
        $this->db->exec("INSERT INTO users (username, password) VALUES ('admin', '" . password_hash('password', PASSWORD_DEFAULT) . "')");
    }

    public function testValidLogin()
    {
        include_once 'public/login.php';
        $_POST['username'] = 'admin';
        $_POST['password'] = 'password';

        ob_start();
        $result = $this->handleLogin($this->db);
        ob_end_clean();

        $this->assertTrue($result);
    }

    public function testInvalidLogin()
    {
        $_POST['username'] = 'invalid';
        $_POST['password'] = 'wrongpassword';

        include_once __DIR__ . '/../public/login.php';

        ob_start();
        $result = $this->handleLogin($this->db);
        ob_end_clean();

        $this->assertFalse($result);
    }

    private function handleLogin($db)
    {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password'])) {
            return true;
        }
        return false;
    }

}
?>
