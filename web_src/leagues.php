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
    header("Location: /web_src/classes/Login/Login.php");
}

$loggedInUserID = $_SESSION['users_id'];

// ------------------------------
// Determine leagueID
// ------------------------------
$leagueID = isset($_GET['leagueID']) ? intval($_GET['leagueID']) : 0;

if (!$leagueID) {
    // Get the user's league if not provided
    $stmt = $connection->prepare("SELECT leagueID FROM users WHERE usersID = ?");
    $stmt->bind_param("i", $loggedInUserID);
    $stmt->execute();
    $stmt->bind_result($leagueID);
    $stmt->fetch();
    $stmt->close();

    if (!$leagueID) {
        die("No league found for your user.");
    }
}
// ------------------------------
// Helper function to calculate total % change for a user
// ------------------------------
function getTotalPercentChange($connection, $userID) {
    if (!$userID) return 0; // handle BYE

    $sql = "
        SELECT h.start_price, h.curr_price
        FROM users_stocks us
        JOIN Holdings h ON us.ticker = h.ticker
        WHERE us.usersID = ?
    ";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $holdings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $total = 0;
    foreach ($holdings as $h) {
        $total += (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
    }
    return $total;
}


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
// Load Round 1 matchups
// ------------------------------
$sql_check_round1 = "
    SELECT matchupID, user_1_id, user_2_id, winner, loser, winner_change, loser_change
    FROM Matchups
    WHERE leagueID = ? AND round = 1
    ORDER BY matchupID ASC
";

$stmt = $connection->prepare($sql_check_round1);
$stmt->bind_param("i", $leagueID);
$stmt->execute();
$round1_result = $stmt->get_result();
$round1_matches = $round1_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ------------------------------
// Generate Round 1 if none
// ------------------------------
if (empty($round1_matches)) {
    $users = $leaderboard;
    shuffle($users);

    $insert = $connection->prepare("
        INSERT INTO Matchups (Week_num, leagueID, user_1_id, user_2_id, winner, loser, round)
        VALUES (?, ?, ?, ?, NULL, NULL, 1)
    ");
    $week = 1;
    $round1_matches = [];

    for ($i = 0; $i < count($users); $i += 2) {
        $u1 = $users[$i]['usersID'];
        $u2 = ($i + 1 < count($users)) ? $users[$i + 1]['usersID'] : null;

        $insert->bind_param("iiii", $week, $leagueID, $u1, $u2);
        $insert->execute();

        $round1_matches[] = [
            'matchupID' => $insert->insert_id,
            'user_1_id' => $u1,
            'user_2_id' => $u2,
            'winner' => null,
            'loser' => null
        ];
    }

    $insert->close();
}

// ------------------------------
// Load all rounds from database
// ------------------------------
$bracket = [];
$maxRoundStmt = $connection->prepare("
    SELECT MAX(round) FROM Matchups WHERE leagueID = ?
");
$maxRoundStmt->bind_param("i", $leagueID);
$maxRoundStmt->execute();
$maxRoundStmt->bind_result($totalRounds);
$maxRoundStmt->fetch();
$maxRoundStmt->close();

// Fetch matchups for each round
for ($r = 1; $r <= $totalRounds; $r++) {
    $stmt = $connection->prepare("
        SELECT matchupID, user_1_id, user_2_id, winner, loser, winner_change, loser_change
        FROM Matchups
        WHERE leagueID = ? AND round = ?
        ORDER BY matchupID ASC
    ");
    $stmt->bind_param("ii", $leagueID, $r);
    $stmt->execute();
    $result = $stmt->get_result();
    $roundMatches = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Attach full user info
    foreach ($roundMatches as &$match) {
        $match['user_1'] = null;
        $match['user_2'] = null;

        foreach ($leaderboard as $u) {
            if ($u['usersID'] == $match['user_1_id']) $match['user_1'] = $u;
            if ($u['usersID'] == $match['user_2_id']) $match['user_2'] = $u;
        }
    }
    unset($match);

    $bracket[$r] = $roundMatches;
}


// ------------------------------
// Load full user info
// ------------------------------
foreach ($bracket as $roundNum => &$matches) {
    foreach ($matches as &$match) {
        $match['user_1'] = null;
        $match['user_2'] = null;

        foreach ($leaderboard as $u) {
            if ($u['usersID'] == $match['user_1_id']) $match['user_1'] = $u;
            if ($u['usersID'] == $match['user_2_id']) $match['user_2'] = $u;
        }
    }
}
unset($match);
unset($matches);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>League Standings & Bracket</title>
    <link rel="stylesheet" href="stylesheets/league.css">
    <link rel="stylesheet" href="stylesheets/matchups.css">
    <style>
        .league-wrapper { display: flex; justify-content: center; gap: 40px; width: 100%; margin-top: 120px; padding: 20px; }
        .standings-container { width: 40%; }
        .bracket-side { width: 50%; }
        table.standings { width: 100%; font-size: 18px; }
        .round { margin-top: 20px; }
        .match { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .player { width: 45%; text-align: center; }
        .vs { width: 10%; text-align: center; }
    </style>
</head>
<body>
<header class="site-header">
    <div class="site-title-container"><h1 class="site-title">Fantasy Stocks</h1></div>
    <nav class="site-nav">
        <ul class="site-nav-list">
            <li><a href="index.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="leagues.php">League</a></li>
            <li><a href="stocks.php">Stocks</a></li>
            <li><a href="about.php">About</a></li>
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
                    <?php
                    // Get %Change for each user
                    
                    // Get %Change for each user safely
                    $user1Percent = isset($match['winner_change'], $match['loser_change']) 
                        ? ($match['winner'] == $match['user_1_id'] ? $match['winner_change'] : $match['loser_change']) 
                        : 0;

                    $user2Percent = isset($match['winner_change'], $match['loser_change']) 
                        ? ($match['winner'] == $match['user_2_id'] ? $match['winner_change'] : $match['loser_change']) 
                        : 0;
                    


                    // Determine display strings
                    $user1Display = $match['user_1'] ? htmlspecialchars($match['user_1']['username']) : "BYE";
                    $user2Display = $match['user_2'] ? htmlspecialchars($match['user_2']['username']) : "BYE";

                    if ($match['winner'] == $match['user_1_id']) {
                        $user1Display = "**{$user1Display}** ~ " . number_format($user1Percent, 2) . "%";
                        $user2Display = "{$user2Display} ~ " . number_format($user2Percent, 2) . "%";
                    } else if ($match['winner'] == $match['user_2_id']) {
                        $user1Display = "{$user1Display} ~ " . number_format($user1Percent, 2) . "%";
                        $user2Display = "**{$user2Display}** ~ " . number_format($user2Percent, 2) . "%";
                    } else { 
                        // matchup not resolved yet
                        $user1Display = "{$user1Display} ~ " . number_format($user1Percent, 2) . "%";
                        $user2Display = "{$user2Display} ~ " . number_format($user2Percent, 2) . "%";
                    }
                    ?>
                    <div class="player"><?php echo $user1Display; ?></div>
                    <div class="vs">vs</div>
                    <div class="player"><?php echo $user2Display; ?></div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>