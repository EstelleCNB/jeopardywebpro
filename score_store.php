<?php

function load_scores_from_file(string $scoresFile): array
{
    $scores = [];

    if (!is_readable($scoresFile)) {
        return $scores;
    }

    $lines = file($scoresFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $parts = preg_split('/\s*[:,|\t]\s*/', trim($line), 2);

        if (count($parts) !== 2) {
            continue;
        }

        [$player, $score] = $parts;

        if ($player === '' || !is_numeric($score)) {
            continue;
        }

        $scores[$player] = (int) $score;
    }

    return $scores;
}

function sync_scores_to_file(string $scoresFile, array $sessionScores): void
{
    $scores = load_scores_from_file($scoresFile);

    foreach ($sessionScores as $player => $score) {
        $scores[$player] = (int) $score;
    }

    arsort($scores);

    $lines = [];
    foreach ($scores as $player => $score) {
        $lines[] = $player . ':' . $score;
    }

    file_put_contents($scoresFile, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
}
