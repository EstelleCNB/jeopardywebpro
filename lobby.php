<?php
// lobby.php — Game lobby; set up a 2-4 player game
// Sprint 4: initialize game state from lobby selections

require_once 'session_guard.php';
require_once __DIR__ . '/score_store.php';

// Initialize game session variables when a new game is started
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_game'])) {
    $playerCount = (int) ($_POST['player_count'] ?? 2);
    if ($playerCount < 2 || $playerCount > 4) {
        $playerCount = 2;
    }

    $players = [$_SESSION['user']];

    for ($slot = 2; $slot <= $playerCount; $slot++) {
        $field = 'player' . $slot;
        $name = htmlspecialchars(trim($_POST[$field] ?? ''));

        if ($name === '') {
            $error = 'Please enter all player usernames for the selected player count.';
            break;
        }

        if (in_array($name, $players, true)) {
            $error = 'Each player username must be unique.';
            break;
        }

        $players[] = $name;
    }

    if (empty($error)) {
        $scores = [];
        foreach ($players as $playerName) {
            $scores[$playerName] = 0;
        }

        $_SESSION['players']         = $players;
        $_SESSION['player_count']    = $playerCount;
        $_SESSION['scores']          = $scores;
        $_SESSION['current_player']  = $_SESSION['user']; // player 1 goes first
        $_SESSION['answered']        = [];                // tracks answered clue IDs
        $_SESSION['daily_double']    = rand(1, 20);       // random clue is daily double

        sync_scores_to_file(__DIR__ . '/scores.txt', $_SESSION['scores']);

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
        <p>You are <strong>Player 1</strong>. Start a game with 2 to 4 total players.</p>

        <?php if ($error): ?><p class="msg error"><?= $error ?></p><?php endif; ?>

        <form method="POST" action="lobby.php">
            <label for="player_count">Total Players</label>
            <select id="player_count" name="player_count">
                <option value="2" <?= (($_POST['player_count'] ?? '2') === '2') ? 'selected' : '' ?>>2 Players</option>
                <option value="3" <?= (($_POST['player_count'] ?? '') === '3') ? 'selected' : '' ?>>3 Players</option>
                <option value="4" <?= (($_POST['player_count'] ?? '') === '4') ? 'selected' : '' ?>>4 Players</option>
            </select>

            <label for="player2">Player 2 Username</label>
            <input type="text" id="player2" name="player2"
                   value="<?= htmlspecialchars($_POST['player2'] ?? '') ?>"
                   required>

                 <div id="player3-group">
                  <label for="player3">Player 3 Username (required for 3-4 players)</label>
                  <input type="text" id="player3" name="player3"
                      value="<?= htmlspecialchars($_POST['player3'] ?? '') ?>"
                      placeholder="Leave blank for 2-player game">
                 </div>

                 <div id="player4-group">
                  <label for="player4">Player 4 Username (required for 4 players)</label>
                  <input type="text" id="player4" name="player4"
                      value="<?= htmlspecialchars($_POST['player4'] ?? '') ?>"
                      placeholder="Leave blank unless 4-player game">
                 </div>

            <button type="submit" name="start_game" class="btn-primary">Start Game</button>
        </form>

        <hr>
        <p><a href="leaderboard.php">View Leaderboard</a></p>
    </main>

    <script>
        (function () {
            var playerCountSelect = document.getElementById('player_count');
            var player3Group = document.getElementById('player3-group');
            var player4Group = document.getElementById('player4-group');
            var player3Input = document.getElementById('player3');
            var player4Input = document.getElementById('player4');

            function updatePlayerFields() {
                var playerCount = Number(playerCountSelect.value);
                var showPlayer3 = playerCount >= 3;
                var showPlayer4 = playerCount >= 4;

                player3Group.style.display = showPlayer3 ? 'block' : 'none';
                player4Group.style.display = showPlayer4 ? 'block' : 'none';

                player3Input.required = showPlayer3;
                player4Input.required = showPlayer4;

                if (!showPlayer3) {
                    player3Input.value = '';
                }

                if (!showPlayer4) {
                    player4Input.value = '';
                }
            }

            playerCountSelect.addEventListener('change', updatePlayerFields);
            updatePlayerFields();
        })();
    </script>
</body>
</html>