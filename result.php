<?php
require_once 'session_guard.php';

if (empty($_SESSION['scores'])) {
    header('Location: lobby.php');
    exit();
}

$results = [];
foreach ($_SESSION['scores'] as $player => $score) {
    $results[] = [
        'player' => $player,
        'score' => (int) $score,
    ];
}

usort($results, static function (array $left, array $right): int {
    return $right['score'] <=> $left['score'];
});

$topScore = $results[0]['score'];
$winners = array_values(array_filter($results, static function (array $entry) use ($topScore): bool {
    return $entry['score'] === $topScore;
}));

$winnerNames = array_map(static function (array $entry): string {
    return $entry['player'];
}, $winners);

$headline = count($winnerNames) === 1
    ? $winnerNames[0] . ' wins!'
    : 'Tie game: ' . implode(' and ', $winnerNames);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! — Results</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="result-page">
    <header class="top-bar">
        <h1 class="logo">Jeopardy!</h1>
        <a href="logout.php" class="btn-secondary">Log Out</a>
    </header>

    <main class="result-wrap">
        <p class="result-kicker">Game Complete</p>
        <h2><?= htmlspecialchars($headline) ?></h2>
        <p class="result-summary">Top score: $<?= number_format($topScore) ?></p>

        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $index => $entry): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($entry['player']) ?></td>
                    <td>$<?= number_format($entry['score']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="result-actions">
            <a href="leaderboard.php" class="btn-primary">View Leaderboard</a>
            <a href="lobby.php" class="btn-secondary">Play Again</a>
        </div>
    </main>
</body>
</html>