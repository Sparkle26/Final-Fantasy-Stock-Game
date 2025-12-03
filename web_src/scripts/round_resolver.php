<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . "/../../data_src/api/includes/db_connect.php";

// --------------------------------------------
// League ID
// --------------------------------------------
$leagueID = isset($_GET['leagueID']) ? intval($_GET['leagueID']) : 1;

// --------------------------------------------
// Determine current round
// --------------------------------------------
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

// --------------------------------------------
// Fetch all unresolved matchups in current round
// --------------------------------------------
$stmt = $connection->prepare("
    SELECT matchupID, user_1_id, user_2_id
    FROM Matchups
    WHERE leagueID = ? AND round = ? AND winner IS NULL
");
$stmt->bind_param("ii", $leagueID, $currentRound);
$stmt->execute();
$matchups = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($matchups)) {
    die("No unresolved matchups found for round $currentRound.");
}

// --------------------------------------------
// Collect winners
// --------------------------------------------
$winners = [];

foreach ($matchups as $match) {
    $user1_id = $match['user_1_id'];
    $user2_id = $match['user_2_id'];

    // BYE case
    if (!$user1_id || !$user2_id) {
        $winner = $user1_id ?: $user2_id;
        $loser = null;
    } else {
        // Fetch holdings for both users
        $sql = "
            SELECT h.start_price, h.curr_price
            FROM users_stocks us
            JOIN Holdings h ON us.ticker = h.ticker
            WHERE us.usersID = ?
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user1_id);
        $stmt->execute();
        $user1_holdings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user2_id);
        $stmt->execute();
        $user2_holdings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Calculate % change
        $total1 = 0;
        foreach ($user1_holdings as $h) {
            $total1 += (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
        }

        $total2 = 0;
        foreach ($user2_holdings as $h) {
            $total2 += (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
        }

        // Determine winner/loser
        if ($total1 >= $total2) {
            $winner = $user1_id;
            $loser = $user2_id;
        } else {
            $winner = $user2_id;
            $loser = $user1_id;
        }

        // Update users table with wins/losses
        if ($winner) {
            $stmt = $connection->prepare("
                UPDATE users
                SET wins = wins + 1
                WHERE usersID = ?
            ");
            $stmt->bind_param("i", $winner);
            $stmt->execute();
            $stmt->close();
        }

        if ($loser) {
            $stmt = $connection->prepare("
                UPDATE users
                SET losses = losses + 1
                WHERE usersID = ?
            ");
            $stmt->bind_param("i", $loser);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Update this matchup with winner + loser
    $stmt = $connection->prepare("
        UPDATE Matchups
        SET winner = ?, loser = ?
        WHERE matchupID = ?
    ");
    $stmt->bind_param("iii", $winner, $loser, $match['matchupID']);
    $stmt->execute();
    $stmt->close();

    // Reset start_price -> curr_price for next round
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

    $winners[] = $winner;
}

// --------------------------------------------
// Pair winners and create next round matchups
// --------------------------------------------
shuffle($winners); // optional: randomize for fairness
for ($i = 0; $i < count($winners); $i += 2) {
    $u1 = $winners[$i];
    $u2 = ($i + 1 < count($winners)) ? $winners[$i + 1] : null; // BYE if odd number

    $stmt = $connection->prepare("
        INSERT INTO Matchups (Week_num, leagueID, user_1_id, user_2_id, round)
        VALUES (?, ?, ?, ?, ?)
    ");
    $week = $nextRound;
    $stmt->bind_param("iiiii", $week, $leagueID, $u1, $u2, $nextRound);
    $stmt->execute();
    $stmt->close();
}

echo "Round $currentRound resolved successfully, next round created.";
?>
