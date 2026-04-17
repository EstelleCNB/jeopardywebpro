<?php
require_once 'session_guard.php';
require_once __DIR__ . '/score_store.php';

$scoresFile = __DIR__ . '/scores.txt';
$leaderboard = [];

foreach (load_scores_from_file($scoresFile) as $player => $score) {
    $leaderboard[] = [
        'player' => $player,
        'score' => $score,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! — Leaderboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="leaderboard-page">
    <header class="top-bar">
        <h1 class="logo">Jeopardy!</h1>
        <a href="logout.php" class="btn-secondary">Log Out</a>
    </header>

    <main class="leaderboard-wrap">
        <h2>Final Scores</h2>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($leaderboard): ?>
                    <?php foreach ($leaderboard as $index => $entry): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($entry['player']) ?></td>
                        <td>$<?= number_format($entry['score']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3">No scores available yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="lobby.php" class="btn-primary">Back to Lobby</a>
    </main>
</body>
</html>