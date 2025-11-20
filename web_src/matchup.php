<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../data_src/api/includes/db_connect.php";

function generateMatchups($connection, $leagueID, $weekNum) {
    // Step 1: Get all users in this league
    $stmt = $connection->prepare("SELECT usersID FROM users WHERE leagueID = ?");
    $stmt->bind_param("i", $leagueID);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (count($users) < 2) {
        echo "League {$leagueID} does not have enough users to create matchups.\n";
        return;
    }

    // Step 2: Shuffle users randomly
    shuffle($users);

    // Step 3: Pair users (round-robin style)
    $pairs = [];
    for ($i = 0; $i < count($users) - 1; $i += 2) {
        if (isset($users[$i + 1])) {
            $pairs[] = [$users[$i]['usersID'], $users[$i + 1]['usersID']];
        } else {
            // Odd number of users: last user gets a bye
            echo "League {$leagueID} Week {$weekNum}: User {$users[$i]['usersID']} gets a bye.\n";
        }
    }

    // Step 4: Insert matchups into the Matchups table
    $stmt = $connection->prepare("
        INSERT INTO Matchups (Week_num, leagueID, user_1_id, user_2_id, winner, loser)
        VALUES (?, ?, ?, ?, NULL, NULL)
    ");

    foreach ($pairs as $pair) {
        $stmt->bind_param("iiii", $weekNum, $leagueID, $pair[0], $pair[1]);
        if (!$stmt->execute()) {
            echo "Error inserting matchup for League {$leagueID}: " . $stmt->error . "\n";
        } else {
            echo "League {$leagueID} Week {$weekNum}: Matchup {$pair[0]} vs {$pair[1]} inserted.\n";
        }
    }

    $stmt->close();
}

// -------------------
// Loop through all leagues
// -------------------
$weekNum = 1; // Change to the week number you want
$leaguesResult = $connection->query("SELECT leagueID FROM League");

while ($league = $leaguesResult->fetch_assoc()) {
    generateMatchups($connection, $league['leagueID'], $weekNum);
}

echo "All matchups generated for week {$weekNum}.\n";
