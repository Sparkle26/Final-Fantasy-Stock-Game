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
/* User Info */
$users_id = $_SESSION['users_id'];
$stmt = $connection->prepare("SELECT username, wins, losses FROM users WHERE usersID = ?");
$stmt->bind_param("i", $users_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/*Stock info */

$stmt = $connection->prepare("SELECT us.ticker, u.username
from users_stocks us JOIN users u ON (us.usersID=u.usersID)
where u.usersID = ?");
$stmt->bind_param("i", $users_id);
$stmt->execute();
$stocksResult = $stmt->get_result();

$userStocks = [];
while ($row = $stocksResult->fetch_assoc()) {
    $userStocks[] = $row['ticker'];
}

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
             <li><a href="about.php">About</a></li>

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

        <form action="classes/uploadImage.php"  method="POST" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" require>
            <input type="submit" value="Upload Image">
        </form>
        
        <div class="stat-box">
            <div class="stat-title">Your Stocks</div>
            <div class="stat-value">
                <?php
                    if (empty($userStocks)) {
                        echo "No stocks yet";
                    } else {
                        echo implode(", ", array_map('htmlspecialchars', $userStocks));
                    }
                ?>
            </div>
        </div>

    </div>
</div>

<a class="logout-btn" href="/web_src/classes/Login/Logout.php">Logout</a>

</body>
</html>
