<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = array();

// Destroy the session completely
session_destroy();

// Redirect to login page
header("Location: index.php");
exit;
?>
