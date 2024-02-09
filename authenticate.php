<?php
include 'config.php';

if ($_POST['password'] == PASSWORD) {
    // Set session variables and redirect to the protected page
    session_start();
    $_SESSION['loggedin'] = true;

    header("Location: index.php");
} else {
    // Authentication failed, redirect back to the login form
    header("Location: login.php");
}
?>
