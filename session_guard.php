<?php
// session_guard.php — include at the top of every protected page
// Usage: require_once 'session_guard.php';
 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php?error=unauthorized');
    exit();
}
?>
