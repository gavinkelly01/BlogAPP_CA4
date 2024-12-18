<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];   
    $password = $_POST['password'];   

    $db = getDB();
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";  
    $stmt = $db->query($query);  

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] === 'ADMIN') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit;
    } else {
        echo "Invalid credentials for $username"; 
    }
}
?>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" ><br>
    <label>Password:</label><br>
    <input type="password" name="password" ><br>
    <button type="submit">Login</button>
</form>
