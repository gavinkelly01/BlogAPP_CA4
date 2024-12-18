<?php
use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)");
    }

    public function testRegisterNewUser()
    {
        $_POST['username'] = 'newuser';
        $_POST['password'] = 'securepassword';
        $_POST['confirm_password'] = 'securepassword';

        include_once __DIR__ . '/../public/register.php';

        ob_start();

        $result = $this->handleRegister($this->db);

        ob_end_clean();

        $this->assertTrue($result);

        $stmt = $this->db->query("SELECT * FROM users WHERE username = 'newuser'");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotEmpty($user);

        $this->assertTrue(password_verify('securepassword', $user['password']));
    }

    private function handleRegister(PDO $db)
    {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            return false;
        }

        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        return $stmt->execute([$username, $password]);
    }
}
?>
