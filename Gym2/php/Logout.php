<?php
session_start(); // Start the session

//  actions if needed

// Destroy the active session of the user
session_destroy();
unset($_COOKIE['active_session']); /* clear cookies */
setcookie('active_session', '', time() - 3600, '/'); // empty value and old timestamp



// Redirect the user to the login page or any other desired page
header("Location: Home.php");
exit();
?>