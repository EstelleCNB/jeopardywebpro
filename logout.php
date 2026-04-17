<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clears all session data stored for the current user.
$_SESSION = [];

// Expires the session cookie so the browser drops the old session ID.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destroys the server-side session and send the user back to login.
session_destroy();

header('Location: login.php');
exit();
