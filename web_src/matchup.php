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
// Get current matchup for logged-in user
// ------------------------------
$leagueID = isset($_GET['leagueID']) ? intval($_GET['leagueID']) : 1;
$currentRound = 1; // adjust if needed

$sql_match = "
    SELECT user_1_id, user_2_id
    FROM Matchups
    WHERE leagueID = ? AND round = ? AND (user_1_id = ? OR user_2_id = ?)
    LIMIT 1
";
$stmt = $connection->prepare($sql_match);
$stmt->bind_param("iiii", $leagueID, $currentRound, $loggedInUserID, $loggedInUserID);
$stmt->execute();
$match = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Determine opponent
if (!$match) {
    die("No matchup found for you in this round.");
}
$opponentID = ($match['user_1_id'] == $loggedInUserID) ? $match['user_2_id'] : $match['user_1_id'];

// ------------------------------
// Fetch logged-in user holdings
// ------------------------------
$sql_user_holdings = "
    SELECT h.ticker, h.start_price, h.curr_price
    FROM users_stocks us
    JOIN Holdings h ON us.ticker = h.ticker
    WHERE us.usersID = ?
";
$stmt = $connection->prepare($sql_user_holdings);
$stmt->bind_param("i", $loggedInUserID);
$stmt->execute();
$user_holdings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ------------------------------
// Fetch opponent holdings and username
// ------------------------------
$sql_opponent_holdings = "
    SELECT u.username, h.ticker, h.start_price, h.curr_price
    FROM users_stocks us
    JOIN Holdings h ON us.ticker = h.ticker
    JOIN users u ON u.usersID = us.usersID
    WHERE us.usersID = ?
";
$stmt = $connection->prepare($sql_opponent_holdings);
$stmt->bind_param("i", $opponentID);
$stmt->execute();
$opponent_holdings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$opponentName = $opponent_holdings[0]['username'] ?? 'Opponent';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Matchup Holdings</title>
    <link rel="stylesheet" href="stylesheets/matchups.css">
    <style>
        .matchup-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            width: 95%;
            margin: 50px auto;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .holdings-container {
            flex: 1;
            max-width: 45%;
        }

        .opponent-side table {
            direction: rtl;
        }

        .holdings-table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .holdings-table th, .holdings-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .holdings-table th {
            background-color: #f3f3f3;
            font-weight: 600;
        }

        .holdings-table td {
            font-weight: 500;
        }

        .holdings-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            padding: 0 24px;
            border-bottom: 1px solid rgba(0,0,0,.1);
            background: #fff;
            position: relative;
            z-index: 10;
        }

        .site-nav ul {
            list-style: none;
            display: flex;
            gap: 8px;
            margin: 0;
            padding: 0;
        }

        .site-nav ul li a {
            padding: 14px 16px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        .site-nav ul li a:hover {
            color: #3DB5E6;
        }

        h1 {
            text-align: center;
            margin-top: 40px;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 700;
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
            <li><a href="stocks.php">Stocks</a></li>
        </ul>
    </nav>
</header>

<h1>Current Matchup</h1>

<div class="matchup-wrapper">
<!-- LEFT: Logged-in user -->
<?php 
$user_total_percent = 0;
foreach ($user_holdings as $h) {
    $user_total_percent += (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
}
?>
<div class="holdings-container user-side">
    <h2>Your Holdings — <?php echo number_format($user_total_percent, 2); ?>%</h2>
    <table class="holdings-table">
        <tr>
            <th>Ticker</th>
            <th>Start Price</th>
            <th>Current Price</th>
            <th>% Change</th>
        </tr>
        <?php foreach ($user_holdings as $h): 
            $percent = (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($h['ticker']); ?></td>
            <td><?php echo number_format($h['start_price'], 2); ?></td>
            <td><?php echo number_format($h['curr_price'], 2); ?></td>
            <td><?php echo number_format($percent, 2); ?>%</td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- RIGHT: Opponent -->
<?php 
$opponent_total_percent = 0;
foreach ($opponent_holdings as $h) {
    $opponent_total_percent += (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
}
?>
<div class="holdings-container opponent-side">
    <h2><?php echo htmlspecialchars($opponentName); ?>'s Holdings — <?php echo number_format($opponent_total_percent, 2); ?>%</h2>
    <table class="holdings-table">
        <tr>
            <th>Ticker</th>
            <th>Start Price</th>
            <th>Current Price</th>
            <th>% Change</th>
        </tr>
        <?php foreach ($opponent_holdings as $h): 
            $percent = (($h['curr_price'] - $h['start_price']) / $h['start_price']) * 100;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($h['ticker']); ?></td>
            <td><?php echo number_format($h['start_price'], 2); ?></td>
            <td><?php echo number_format($h['curr_price'], 2); ?></td>
            <td><?php echo number_format($percent, 2); ?>%</td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>


</div>

</body>
</html>
