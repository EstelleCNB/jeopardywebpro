<?php
// login.php — User authentication
// Sprint 3: Wire up password_verify() against data/users.txt

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: lobby.php');
    exit();
}

$error = '';

// Show error messages passed via redirect
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'unauthorized') {
        $error = 'You must be logged in to play.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        // Sprint 3 TODO: read data/users.txt, find user, run password_verify()
        // For now, accept any non-empty login so navigation can be tested
        // --- REMOVE the block below in Sprint 3 and replace with real auth ---
        $_SESSION['logged_in'] = true;
        $_SESSION['user']      = $username;
        header('Location: lobby.php');
        exit();
        // --- END placeholder ---

        // Sprint 3 real auth skeleton:
        // $users = file('data/users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        // $authenticated = false;
        // foreach ($users as $line) {
        //     [$storedUser, $storedHash] = explode(':', $line, 2);
        //     if ($storedUser === $username && password_verify($password, $storedHash)) {
        //         $authenticated = true;
        //         break;
        //     }
        // }
        // if ($authenticated) {
        //     $_SESSION['logged_in'] = true;
        //     $_SESSION['user']      = $username;
        //     header('Location: lobby.php');
        //     exit();
        // } else {
        //     $error = 'Invalid username or password.';
        // }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! — Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h1 class="logo">Jeopardy!</h1>
        <h2>Sign In</h2>

        <?php if ($error): ?><p class="msg error"><?= $error ?></p><?php endif; ?>

        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn-primary">Log In</button>
        </form>

        <p class="alt-link">New player? <a href="register.php">Create an account</a></p>
    </div>
</body>
</html>