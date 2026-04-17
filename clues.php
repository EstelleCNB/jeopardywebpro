<?php
// clue.php — Display clue + handle answer submission
// Sprint 4: Add similar_text() matching, daily double wager

require_once 'session_guard.php';
require_once 'questions.php';

if (!isset($_SESSION['scores'])) {
    header('Location: lobby.php');
    exit();
}

$id = intval($_GET['id'] ?? 0);

// Validate clue ID
if (!isset($clues[$id])) {
    header('Location: game.php');
    exit();
}

// Prevent re-answering
if (in_array($id, $_SESSION['answered'] ?? [])) {
    header('Location: game.php');
    exit();
}

$clue          = $clues[$id];
$currentPlayer = $_SESSION['current_player'];
$isDD          = ($id === $_SESSION['daily_double']);
$feedback      = '';
$correct       = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawAnswer = trim($_POST['answer'] ?? '');
    $userAnswer = strtolower(htmlspecialchars($rawAnswer));
    $rightAnswer = strtolower($clue['question']);

    // Case-insensitive fuzzy match with similar_text()
    similar_text($userAnswer, $rightAnswer, $pct);
    $correct = ($pct >= 70); // 70% similarity threshold

    $points = $clue['value'];

    if ($correct) {
        $_SESSION['scores'][$currentPlayer] += $points;
        $feedback = "✅ Correct! +\$$points";
    } else {
        $_SESSION['scores'][$currentPlayer] -= $points;
        $feedback = "❌ Wrong! -\$$points. Correct: \"What is {$clue['question']}?\"";
    }

    // Mark clue as answered and flip turn
    $_SESSION['answered'][] = $id;
    $players = array_keys($_SESSION['scores']);
    $currentIdx = array_search($currentPlayer, $players);
    $_SESSION['current_player'] = $players[($currentIdx + 1) % count($players)];

    // Check game over
    if (count($_SESSION['answered']) >= 20) {
        header('Location: result.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! — Clue</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="clue-page">

    <header class="top-bar">
        <h1 class="logo">Jeopardy!</h1>
        <div class="scores">
            <?php foreach ($_SESSION['scores'] as $player => $score): ?>
                <span class="score-badge <?= ($player === $currentPlayer) ? 'active' : '' ?>">
                    <?= htmlspecialchars($player) ?>: $<?= number_format($score) ?>
                </span>
            <?php endforeach; ?>
        </div>
    </header>

    <main class="clue-card">
        <p class="category-label">
            <?= htmlspecialchars($clue['category']) ?> — $<?= $clue['value'] ?>
            <?= $isDD ? '<span class="dd-badge">Daily Double!</span>' : '' ?>
        </p>

        <div class="clue-answer-text">
            <?= htmlspecialchars($clue['answer']) ?>
        </div>

        <?php if ($correct === null): ?>
        <!-- Answer form — only shown before submission -->
        <form method="POST" action="clue.php?id=<?= $id ?>">
            <label for="answer">
                <?= htmlspecialchars($currentPlayer) ?>, what is your answer?
            </label>
            <input type="text" id="answer" name="answer" autofocus required
                   placeholder='Type "What is ..."'>
            <button type="submit" class="btn-primary">Submit Answer</button>
        </form>

        <?php else: ?>
        <!-- Feedback after submission -->
        <p class="feedback <?= $correct ? 'correct' : 'wrong' ?>"><?= $feedback ?></p>
        <a href="game.php" class="btn-primary">Back to Board</a>
        <?php endif; ?>
    </main>

</body>
</html>