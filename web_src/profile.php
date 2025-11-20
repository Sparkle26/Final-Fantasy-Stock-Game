<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../data_src/api/includes/db_connect.php';

if (!isset($_SESSION['users_id'])) {
    header("Location: /web_src/classes/Login/Login.php");
    exit();
}

$users_id = $_SESSION['users_id'];
$stmt = $connection->prepare("SELECT username, wins, losses FROM users WHERE usersID = ?");
$stmt->bind_param("i", $users_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="stylesheets/profile.css">
</head>

<body>

<header class="site-header">
    <h1 class="site-title">Fantasy Stocks</h1>
    <nav class="site-nav">
        <ul class="site-nav-list">
            <li><a href="index.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="leagues.php">League</a></li>
            <li><a href="stocks.php">Stocks</a></li>
        </ul>
    </nav>
</header>

<h2 class="welcome-title">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>

<div class="profile-section">

    <div class="profile-img aya-image"></div>

    <div class="stats-row">

        <div class="stat-box">
            <div class="win-stat-title">Wins</div>
            <div class="stat-value"><?php echo $user['wins']; ?></div>
        </div>

        <div class="stat-box">
            <div class="loss-stat-title">Losses</div>
            <div class="stat-value"><?php echo $user['losses']; ?></div>
        </div>

        <div class="stat-box">
            <div class="stat-title">Streak</div>
            <div class="stat-value">0</div>
        </div>

    </div>
</div>

<a class="logout-btn" href="/web_src/classes/Login/Logout.php">Logout</a>

</body>
</html>
