<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../data_src/api/includes/db_connect.php";

// ------------------------------
// Optional: run the Python scraper first
// ------------------------------
$pythonScript = __DIR__ . "/fin.py";

// On Windows, use 'python', on Unix use 'python3'
exec("python " . escapeshellarg($pythonScript), $output, $returnVar);
if ($returnVar !== 0) {
    echo "Python script output:\n";
    echo implode("\n", $output);
    die("Error running fin.py. Check your Python script.");
}

// ------------------------------
// League ID (from GET or default to 1)
// ------------------------------
$leagueID = isset($_GET['leagueID']) ? intval($_GET['leagueID']) : 1;

// ------------------------------
// Determine the current and next rounds
// ------------------------------
$stmt = $connection->prepare("
    SELECT MAX(round) AS last_round
    FROM Matchups
    WHERE leagueID = ?
");
$stmt->bind_param("i", $leagueID);
$stmt->execute();
$stmt->bind_result($lastRound);
$stmt->fetch();
$stmt->close();

$currentRound = $lastRound ? $lastRound : 1;
$nextRound = $currentRound + 1;

// ------------------------------
// Get all matchups in the current round
// ------------------------------
$stmt = $connection->prepare("
    SELECT m.id, m.user_1_id, m.user_2_id
    FROM Matchups m
    WHERE m.leagueID = ? AND m.round = ?
");
$stmt->bind_param("ii", $leagueID, $currentRound);
$stmt->execute();
$matchups = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ------------------------------
// Resolve each matchup
// ------------------------------
foreach ($matchups as $match) {
    $user1_id = $match['user_1_id'];
    $user2_id = $match['user_2_id'];

    // BYE handling
    if (!$user1_id || !$user2_id) {
        $winner = $user1_id ?: $user2_id;
        $loser = null;
    } else {
        // Get total % change for both users
        $sql = "
            SELECT h.start_price, h.curr_price
            FROM users_stocks us
            JOIN Holdings h ON us.ticker = h.ticker
            WHERE us.usersID = ?
        ";

        // User 1
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user1_id);
        $stmt->execute();
        $user1_holdings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // User 2
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user2_id);
        $stmt->execute();
        $user2_holdings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $total1 = 0;
        foreach ($user1_holdings as $h) {
            $total1 += (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
        }

        $total2 = 0;
        foreach ($user2_holdings as $h) {
            $total2 += (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
        }

        if ($total1 >= $total2) {  // in case of tie, user1 wins
            $winner = $user1_id;
            $loser = $user2_id;
        } else {
            $winner = $user2_id;
            $loser = $user1_id;
        }
    }

    // ------------------------------
    // Update current matchup with winner/loser
    // ------------------------------
    $stmt = $connection->prepare("
        UPDATE Matchups
        SET winner = ?, loser = ?
        WHERE id = ?
    ");
    $stmt->bind_param("iii", $winner, $loser, $match['id']);
    $stmt->execute();
    $stmt->close();

    // ------------------------------
    // Move current prices to start prices for both users
    // ------------------------------
    foreach ([$user1_id, $user2_id] as $uid) {
        if (!$uid) continue;
        $stmt = $connection->prepare("
            UPDATE Holdings h
            JOIN users_stocks us ON us.ticker = h.ticker
            SET h.start_price = h.curr_price
            WHERE us.usersID = ?
        ");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $stmt->close();
    }

    // ------------------------------
    // Insert next round matchup (auto-advance)
    // ------------------------------
    $stmt = $connection->prepare("
        INSERT INTO Matchups (Week_num, leagueID, user_1_id, round)
        VALUES (?, ?, ?, ?)
    ");
    $week = $nextRound; // can be same as next round
    $stmt->bind_param("iiii", $week, $leagueID, $winner, $nextRound);
    $stmt->execute();
    $stmt->close();
}

echo "Round $currentRound resolved successfully.";
?>
