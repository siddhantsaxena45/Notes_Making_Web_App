<?php
// Start the session to access session variables
session_start();

// Unset all session variables, effectively clearing any stored user data
session_unset();

// Destroy the session, fully logging the user out
session_destroy();

// Redirect the user to the login page after logout
header("location: login.php");

// Ensure the script stops executing after the redirect
exit;
?>
