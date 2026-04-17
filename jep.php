<?php
// game.php — Jeopardy board display
// Sprint 4: Board pulls live from questions.php, answered cells marked

require_once 'session_guard.php';
require_once 'questions.php'; // loads $categories and $clues

// Guard: must have an active game
if (!isset($_SESSION['scores'])) {
    header('Location: lobby.php');
    exit();
}

$currentPlayer = $_SESSION['current_player'];
$answered      = $_SESSION['answered'] ?? [];

// Check if all 20 clues are done → go to result
if (count($answered) >= 20) {
    header('Location: result.php'); // FIX 4: was 'reslt.php'
    exit();
}

// Group clues by category for board rendering
$board = [];
foreach ($clues as $id => $clue) {
    $board[$clue['category']][$clue['value']] = $id;
}
$dollarValues = [200, 400, 600, 800];
?> <!-- FIX 1: close PHP block before HTML -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jeopardy! — Game Board</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="game-page">

    <header class="top-bar">
        <h1 class="logo">Jeopardy!</h1>
        <div class="scores">
            <?php foreach ($_SESSION['scores'] as $player => $score): ?>
                <span class="score-badge <?= ($player === $currentPlayer) ? 'active' : '' ?>">
                    <?= htmlspecialchars($player) ?>: $<?= number_format($score) ?>
                </span>
            <?php endforeach; ?>
        </div>
        <a href="logout.php" class="btn-secondary">Log Out</a>
    </header>

    <p class="turn-notice">
        It's <strong><?= htmlspecialchars($currentPlayer) ?></strong>'s turn.
    </p>

    <main class="board-wrap">
        <table class="jeopardy-board">
            <thead>
                <tr> <!-- FIX 2: was <t> -->
                    <?php foreach ($categories as $cat): ?> <!-- FIX 3: was ($categories as $cat: -->
                        <th><?= htmlspecialchars($cat) ?></th>
                    <?php endforeach; ?>
                </tr> <!-- FIX 2: was </t> -->
            </thead>
            <tbody>
                <?php foreach ($dollarValues as $val): ?>
                <tr>
                    <?php foreach ($categories as $cat):
                        $id = $board[$cat][$val] ?? null;
                        $done = in_array($id, $answered);
                    ?>
                        <td class="clue-cell <?= $done ? 'answered' : '' ?>">
                            <?php if (!$done && $id): ?>
                                <a href="clue.php?id=<?= $id ?>">$<?= $val ?></a>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

</body>
</html>