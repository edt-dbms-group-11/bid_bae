<?php

session_start();

unset($_SESSION['is_logged_in']);
unset($_SESSION['username']);
unset($_SESSION['display_name']);
unset($_SESSION['email']);
unset($_SESSION['id']);

setcookie(session_name(), "", time() - 360);
session_destroy();

// Redirect to index 
header("Location: index.php");

?>