<?php
session_start();
require_once "../data_src/api/includes/db_connect.php";
require_once 'classes/NavBar.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: classes/Login/Login.php");
    exit();
}

// Fetch league info

// $stmt = $pdo->prepare("
//                         SELECT u.leagueID, u.username, u.wins, u.losses, l.league_name
//                         FROM user u
//                         JOIN league l ON u.leagueID = l.leagueID
//                         ORDER BY u.wins DESC
//                     ");
// $stmt->execute();
// $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>League Page</title>
    <link rel="stylesheet" href="stylesheets/league.css">
    <link rel="stylesheet" href="stylesheets/styles.css"></head>
<body>
    <header class="site-header">
    <h1 class="site-title">Fantasy Stocks</h1>
    <nav class = "site-nav">
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="leagues.php">League</a></li>
        <li><a href="about.php">About</a></li>
    </ul>
    </header>

    <?php if (!empty($users)): ?>
        <h1 style="margin-top:20px; margin-bottom:20px; margin-left:20px">
            Welcome to <?php echo htmlspecialchars($users[0]['league_name']); ?>!
        </h1>
        <table>
            <tr>
                <th style="text-align:center">Username</th>
                <th style="text-align:center">Wins</th>
                <th style="text-align:center">Losses</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td style="text-align:center">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </td>
                    <td style="text-align:center">
                        <?php echo htmlspecialchars($user['wins']); ?>
                    </td>
                    <td style="text-align:center">
                        <?php echo htmlspecialchars($user['losses']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <h1 style="margin-top:20px; margin-bottom:20px; margin-left:20px">
            No users found in the league.
        </h1>

        <!-- TEMP WHILE DATABASE IS SET UP -->
        <div style="text-align:center; margin-top:20px; margin-bottom:20px">*Example of what the page will show*</div>
        <table>
            <tr>
                <th style="text-align:center">Username</th>
                <th style="text-align:center">Wins</th>
                <th style="text-align:center">Losses</th>
            </tr>
            <tr>
                <td style="text-align:center">Ryder</td>
                <td style="text-align:center">10</td>
                <td style="text-align:center">4</td>
            </tr>
            <tr>
                <td style="text-align:center">Aya</td>
                <td style="text-align:center">7</td>
                <td style="text-align:center">7</td>
            </tr>
            <tr>
                <td style="text-align:center">Paul</td>
                <td style="text-align:center">6</td>
                <td style="text-align:center">8</td>
            </tr>
        </table>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</body>
</html>