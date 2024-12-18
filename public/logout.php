<?php
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
]);
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
?>
