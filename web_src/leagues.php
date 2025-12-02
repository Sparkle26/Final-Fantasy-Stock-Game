<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../data_src/api/includes/db_connect.php";

// ------------------------------
// Ensure user logged in
// ------------------------------
if (!isset($_SESSION['users_id'])) {
    die("You must be logged in to view this page.");
}

$loggedInUserID = $_SESSION['users_id'];

// ------------------------------
// Get leagueID (from GET or session) — default to 1
// ------------------------------
$leagueID = isset($_GET['leagueID']) ? intval($_GET['leagueID']) : 1;

// ------------------------------
// Fetch users in league for leaderboard
// ------------------------------
$sql_leaderboard = "
    SELECT usersID, username, wins, losses 
    FROM users 
    WHERE leagueID = ?
    ORDER BY wins DESC, losses ASC
";

$stmt = $connection->prepare($sql_leaderboard);
$stmt->bind_param("i", $leagueID);
$stmt->execute();
$leaderboard_result = $stmt->get_result();

$leaderboard = [];
while ($row = $leaderboard_result->fetch_assoc()) {
    $leaderboard[] = $row;
}
$stmt->close();

if (empty($leaderboard)) {
    die("No users found in this league.");
}

// ------------------------------
// Build list of users for bracket generation
// ------------------------------
$users = $leaderboard; // same array structure

// ------------------------------
// Step 1: Shuffle users
// ------------------------------
shuffle($users);

// ------------------------------
// Step 2: Generate Round 1 matches dynamically
// ------------------------------
$round1_matches = [];
$numUsers = count($users);

for ($i = 0; $i < $numUsers; $i += 2) {
    $user1 = $users[$i];
    $user2 = ($i + 1 < $numUsers) ? $users[$i + 1] : null; // Only add BYE if unmatched
    $round1_matches[] = ['user_1' => $user1, 'user_2' => $user2];
}

$bracket = [];
$bracket[1] = $round1_matches;

// ------------------------------
// Step 3: Auto-advance BYE winners for later rounds
// ------------------------------
$totalRounds = ceil(log(max(1, count($round1_matches) * 2), 2));

for ($r = 2; $r <= $totalRounds; $r++) {
    $prevRound = $bracket[$r - 1];
    $rnd = [];

    for ($i = 0; $i < count($prevRound); $i += 2) {
        $u1 = null;
        $u2 = null;

        // Pick first available winner from previous round
        if (isset($prevRound[$i]['user_1']) && $prevRound[$i]['user_2'] === null) {
            $u1 = $prevRound[$i]['user_1']; // BYE winner auto-advance
        } else if (isset($prevRound[$i]['user_1']) && isset($prevRound[$i]['user_2'])) {
            $u1 = null; // actual match, winner TBD
        }

        // Same for next match
        if (isset($prevRound[$i + 1]['user_1']) && $prevRound[$i + 1]['user_2'] === null) {
            $u2 = $prevRound[$i + 1]['user_1']; // BYE winner auto-advance
        } else if (isset($prevRound[$i + 1]['user_1']) && isset($prevRound[$i + 1]['user_2'])) {
            $u2 = null; // actual match, winner TBD
        }

        $rnd[] = ['user_1' => $u1, 'user_2' => $u2];
    }

    $bracket[$r] = $rnd;
}


// ------------------------------
// Step 4: Insert Round 1 Matchups into DB if not inserted
// ------------------------------

// Check if Round 1 already exists
$chk = $connection->prepare("
    SELECT COUNT(*) 
    FROM Matchups 
    WHERE leagueID = ? AND round = 1
");
$chk->bind_param("i", $leagueID);
$chk->execute();
$chk->bind_result($existingCount);
$chk->fetch();
$chk->close();

if ($existingCount == 0) {

    $insert = $connection->prepare("
        INSERT INTO Matchups 
        (Week_num, leagueID, user_1_id, user_2_id, winner, loser, round)
        VALUES (?, ?, ?, ?, NULL, NULL, 1)
    ");

    $week = 1;

    foreach ($round1_matches as $match) {
        $u1 = $match['user_1'] ? $match['user_1']['usersID'] : null;
        $u2 = $match['user_2'] ? $match['user_2']['usersID'] : null;

        // Skip inserting if both users are null (no matchup)
        if ($u1 === null && $u2 === null) {
            continue;
        }

        $insert->bind_param("iiii", $week, $leagueID, $u1, $u2);
        $insert->execute();
    }

    $insert->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>League Standings & Bracket</title>
    <link rel="stylesheet" href="stylesheets/league.css">
    <link rel="stylesheet" href="stylesheets/matchups.css">

    <style>
        /* NEW LAYOUT */
        .league-wrapper {
            display: flex;
            justify-content: center;
            gap: 40px;
            width: 100%;
            margin-top: 120px;
            padding: 20px;
        }

        .standings-container {
            width: 40%;
        }

        .bracket-side {
            width: 50%;
        }

        table.standings {
            width: 100%;
            font-size: 18px;
        }

        .round {
            margin-top: 20px;
        }
    </style>
</head>

<body>

<header class="site-header">
    <div class="site-title-container">
        <h1 class="site-title">Fantasy Stocks</h1>
    </div>
    <nav class="site-nav">
        <ul class="site-nav-list">
            <li><a href="index.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="leagues.php">League</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="stocks.php">Stocks</a></li>
        </ul>
    </nav>
</header>

<h1 class="page-title">League Standings & Bracket</h1>

<div class="league-wrapper">

    <!-- LEFT SIDE: LEADERBOARD -->
    <div class="standings-container">
        <h2>League Standings</h2>
        <table class="standings">
            <tr>
                <th>Username</th>
                <th>Wins</th>
                <th>Losses</th>
            </tr>

            <?php foreach ($leaderboard as $u): ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                    <td><?php echo $u['wins']; ?></td>
                    <td><?php echo $u['losses']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- RIGHT SIDE: BRACKET -->
    <div class="bracket-side">
        <h2>Playoff Bracket</h2>

        <div class="bracket-container">
            <?php foreach ($bracket as $roundNum => $matches): ?>
                <div class="round">
                    <h3>Round <?php echo $roundNum; ?></h3>

                    <?php foreach ($matches as $match): ?>
                        <div class="match">
                            <div class="player">
                                <?php echo $match['user_1'] ? htmlspecialchars($match['user_1']['username']) : "BYE"; ?>
                            </div>
                            <div class="vs">vs</div>
                            <div class="player">
                                <?php echo $match['user_2'] ? htmlspecialchars($match['user_2']['username']) : "BYE"; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php endforeach; ?>
        </div>

    </div>

</div>

</body>
</html>
