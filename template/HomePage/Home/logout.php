<?php
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();
setcookie("remembered_username", "", time() - 3600, "/");
// Redirect to the login page
header("Location: ../landingpage/index.html");
?>
