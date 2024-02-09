<?php
session_start();

// Destroy session and redirect to login page
$_SESSION = array();
session_destroy();

header("Location: login.php");
exit;
?>
