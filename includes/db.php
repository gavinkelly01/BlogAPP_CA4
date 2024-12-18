<?php
function getDB() {
    try {
        $db = new PDO('sqlite:../db/BlogDB.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error reporting
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Set the fetch mode to associative arrays
        return $db;
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        exit;
    }
}
?>
