<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once "../data_src/api/includes/db_connect.php"; // mysqli connection

// Ensure database connection exists
if (!isset($connection) || !$connection) {
    die("Database connection not found.");
}

// Correct session check
$isLoggedIn = isset($_SESSION['users_id']);

// ------------------------
// LOGGED-IN VIEW
// ------------------------
if ($isLoggedIn) {

    // 1. Get the user's leagueID
    $stmt = $connection->prepare("SELECT leagueID FROM users WHERE usersID = ?");
    $stmt->bind_param("i", $_SESSION['users_id']);

    if (!$stmt->execute()) {
        die("Failed to get user's league: " . $stmt->error);
    }

    $res = $stmt->get_result();
    $userLeague = $res->fetch_assoc();
    $stmt->close();

    $leagueID = $userLeague['leagueID'] ?? null;

    // 2. Fetch leaderboard
    if ($leagueID) {
        $stmt = $connection->prepare("
            SELECT 
                u.username, 
                u.wins, 
                u.losses, 
                l.leagueName
            FROM users u
            JOIN League l 
                ON u.leagueID = l.leagueID
            WHERE u.leagueID = ?
            ORDER BY u.wins DESC
        ");

        $stmt->bind_param("i", $leagueID);

        if (!$stmt->execute()) {
            die("Failed to get leaderboard: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $users = [];
    }

} 
// ------------------------
// NOT LOGGED-IN VIEW
// ------------------------
else {
    $sql = "
        SELECT 
            l.leagueID,
            l.leagueName,
            l.duration,
            COUNT(u.usersID) AS num_users
        FROM League l
        LEFT JOIN users u 
            ON l.leagueID = u.leagueID
        GROUP BY 
            l.leagueID, 
            l.leagueName, 
            l.duration
        ORDER BY l.leagueName ASC
    ";

    $result = $connection->query($sql);

    if (!$result) {
        die("Failed to fetch leagues: " . $connection->error);
    }

    $leagues = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Leagues</title>
    <link rel="stylesheet" href="profile.css">
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
            <li><a href="about.php">About</a></li>
            <li><a href="leagues.php">League</a></li>
        </ul>
    </nav>
</header>

<?php if ($isLoggedIn && !empty($users)): ?>
    <h1>Leaderboard for <?php echo htmlspecialchars($users[0]['leagueName']); ?></h1>

    <table border="1">
        <tr>
            <th>Username</th>
            <th>Wins</th>
            <th>Losses</th>
        </tr>

        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['wins']); ?></td>
                <td><?php echo htmlspecialchars($user['losses']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php elseif (!$isLoggedIn && !empty($leagues)): ?>

    <h1>All Leagues</h1>

    <table border="1">
        <tr>
            <th>League Name</th>
            <th>Duration</th>
            <th>Number of Users</th>
        </tr>

        <?php foreach ($leagues as $league): ?>
            <tr>
                <td><?php echo htmlspecialchars($league['leagueName']); ?></td>
                <td><?php echo htmlspecialchars($league['duration']); ?></td>
                <td><?php echo htmlspecialchars($league['num_users']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

<?php else: ?>
    <p>No data available.</p>
<?php endif; ?>

<a href="/web_src/classes/Login/Logout.php">Logout</a>

</body>
</html>
