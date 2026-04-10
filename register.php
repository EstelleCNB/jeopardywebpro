<?php
 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: lobby.php');
    exit();
}
 
$error   = '';
$success = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';
 
    if (empty($username) || empty($password) || empty($confirm)) {
        $error = 'All fields are required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $success = 'Account created! <a href="login.php">Log in here</a>.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! — Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h1 class="logo">Jeopardy!</h1>
        <h2>Create Account</h2>
 
        <?php if ($error):   ?><p class="msg error"><?= $error ?></p><?php endif; ?>
        <?php if ($success): ?><p class="msg success"><?= $success ?></p><?php endif; ?>
 
        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   required minlength="3">
 
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6">
 
            <label for="confirm">Confirm Password</label>
            <input type="password" id="confirm" name="confirm" required>
 
            <button type="submit" class="btn-primary">Register</button>
        </form>
 
        <p class="alt-link">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>