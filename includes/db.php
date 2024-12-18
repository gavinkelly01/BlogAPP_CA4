<?php
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $dbPath = realpath(__DIR__ . '/../db/blogDB.db');
            if ($dbPath === false) {
                throw new Exception('Database file not found.');
            }

            $db = new PDO('sqlite:' . $dbPath);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            echo 'A database connection error occurred. Please try again later.';
            exit;
        } catch (Exception $e) {
            error_log('General error in getDB: ' . $e->getMessage());
            echo 'A system error occurred. Please contact support.';
            exit;
        }
    }
    return $db;
}
?>
