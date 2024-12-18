<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute([':username' => $username, ':password' => $password]);

    header("Location: login.php");
}
?>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" ><br>
    <label>Password:</label><br>
    <input type="password" name="password" ><br>
    <button type="submit">Register</button>
</form>
