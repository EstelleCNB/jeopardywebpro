<?php
// lobby.php — Game lobby; players pair up and start a game
// Sprint 3/4: Add second-player login flow, game initialization

require_once 'session_guard.php';

// Initialize game session variables when a new game is started
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_game'])) {
    $player2 = htmlspecialchars(trim($_POST['player2'] ?? ''));

    if (empty($player2)) {
        $error = 'Please enter Player 2 username.';
    } else {
        // Sprint 4 TODO: pull real board from questions.php
        $_SESSION['player1']         = $_SESSION['user'];
        $_SESSION['player2']         = $player2;
        $_SESSION['scores']          = [$_SESSION['user'] => 0, $player2 => 0];
        $_SESSION['current_player']  = $_SESSION['user']; // player 1 goes first
        $_SESSION['answered']        = [];                // tracks answered clue IDs
        $_SESSION['daily_double']    = rand(1, 20);       // random clue is daily double

        header('Location: jep.php');
        exit();
    }
}

$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! — Lobby</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="lobby-page">
    <header class="top-bar">
        <h1 class="logo">Jeopardy!</h1>
        <span>Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></span>
        <a href="logout.php" class="btn-secondary">Log Out</a>
    </header>

    <main class="lobby-card">
        <h2>Game Lobby</h2>
        <p>You are <strong>Player 1</strong>. Enter Player 2's username to start.</p>

        <?php if ($error): ?><p class="msg error"><?= $error ?></p><?php endif; ?>

        <form method="POST" action="lobby.php">
            <label for="player2">Player 2 Username</label>
            <input type="text" id="player2" name="player2"
                   value="<?= htmlspecialchars($_POST['player2'] ?? '') ?>"
                   required>
            <button type="submit" name="start_game" class="btn-primary">Start Game</button>
        </form>

        <hr>
        <p><a href="leaderboard.php">View Leaderboard</a></p>
    </main>
</body>
</html>