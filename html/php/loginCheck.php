<?php
session_start();

// Check if there is a user logged in. If there is not, then redirect to the login page.
if(!isset($_SESSION["userType"])) {
    header("Location: index.php");
}
?>