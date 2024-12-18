<?php
include '../includes/db.php';
include '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = getDB();
    $db->prepare("INSERT INTO users (username, password) VALUES ('$username', '$password')")->execute();

    header("Location: login.php");
    exit;
}
?>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username"><br>
    <label>Password:</label><br>
    <input type="password" name="password"><br>
    <button type="submit">Register</button>
</form>
