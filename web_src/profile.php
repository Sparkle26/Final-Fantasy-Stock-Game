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

/* Handle Save My Stock List submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stocks'])) {

// Clear existing stocks (optional depending on your design)
    $del = $connection->prepare("DELETE FROM users_stocks WHERE usersID = ?");
    $del->bind_param("i", $users_id);
    $del->execute();

// Insert selected stocks
    $ins = $connection->prepare("INSERT INTO users_stocks (usersID, ticker) VALUES (?, ?)");

    foreach ($_POST['stocks'] as $ticker) {
        $ins->bind_param("is", $users_id, $ticker);
        $ins->execute();
    }

    // Refresh page so updated list shows immediately
    header("Location: profile.php");
    exit();
}
/* Check if user is currently in a league */
$stmt = $connection->prepare("SELECT leagueID FROM users WHERE usersID = ?");
$stmt->bind_param("i", $users_id);
$stmt->execute();
$inLeagueRes = $stmt->get_result();
$inLeagueRow = $inLeagueRes->fetch_assoc();
$userLeague = $inLeagueRow['leagueID'];
/* Fetch all leagues if user is NOT in one */
$availableLeagues = [];
if ($userLeague === null) {

    // Pull ALL leagues
    $sql = "SELECT leagueID, leagueName FROM League";
    $res = $connection->query($sql);
    if (!$res) {
    die("League query failed: " . $connection->error);
}

    while ($row = $res->fetch_assoc()) {
        $availableLeagues[] = $row;
    }
}
/* Handle Join League */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_league'])) {

    $leagueID = intval($_POST['join_league']);

    // Update user's league column
    $stmt = $connection->prepare("UPDATE users SET leagueID = ? WHERE usersID = ?");
    $stmt->bind_param("ii", $leagueID, $users_id);
    $stmt->execute();

    // Redirect to refresh the page with the new state
    header("Location: profile.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="stylesheets/profile.css">
    <link rel="stylesheet" href="stylesheets/stocks.css">
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
            <li><a href="about.php">About</a></li>
            <li><a href="matchup.php">Matchup</a></li>
        </ul>
    </nav>
</header>

<h2 class="welcome-title">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>

<div class="profile-section">
    <div class="profile-img aya-image"></div>
    
    <div class="stats-wrapper">
        <div class="stats-group">

            <div class="stat-box">
                <div class="win-stat-title">Wins</div>
                <div class="stat-value"><?php echo $user['wins']; ?></div>
            </div>

            <div class="stat-box">
                <div class="loss-stat-title">Losses</div>
                <div class="stat-value"><?php echo $user['losses']; ?></div>
            </div>

            <div class="stat-box">
                <div class="streak-title">Streak</div>
                <div class="stat-value">0</div>
            </div>

        </div>

        <div class="stocks">
            <div class="stocks-title">Your Stocks</div>
            <div class="stocks-value">
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

<form action="classes/uploadImage.php" method="POST" enctype="multipart/form-data" class="upload-form">
    <label for="fileToUpload" class="upload-btn">Upload Image</label>
    <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" onchange="this.form.submit()">
</form>


<div class="stocks-container">
    <form action="#" method="POST" class="stock-picker-form">
        <ul class="stocks-list">
            <?php
            $sql = "SELECT h.ticker, h.st_name, s.sectorName, h.start_price 
                    FROM Holdings h
                    JOIN Sector s ON h.index = s.index";
            $result = $connection->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $ticker = htmlspecialchars($row['ticker']);
                    $name = htmlspecialchars($row['st_name']);
                    $sector = htmlspecialchars($row['sectorName']);
                    $price = number_format($row['start_price'], 2);

                    echo '<li class="stock-item">';
                    echo '<label>';
                    echo '<input type="checkbox" name="stocks[]" value="'. $ticker .'"> ';
                    echo $ticker . ' – ' . $name . ' (' . $sector . ') – $' . $price;
                    echo '</label>';
                    echo '</li>';
                }
            } else {
                echo '<p>No stocks found</p>';
            }
            ?>
        </ul>
        <button type="submit" class="save-stocks-btn">Save My Stock List</button>
    </form>
</div>

<?php if ($userLeague === null): ?>
<div class="league-browser">
    <h2 class="league-browser-title">Join a League</h2>

    <?php if (empty($availableLeagues)): ?>
        <p>No active leagues available right now.</p>
    <?php else: ?>
        <table class="league-table">
            <tr>
                <th>League Name</th>
            </tr>

            <?php foreach ($availableLeagues as $lg): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lg['leagueName']); ?></td>
                    
                    <td>
                        <form method="POST">
                            <button type="submit"
                                    name="join_league"
                                    value="<?php echo $lg['leagueID']; ?>"
                                    class="join-btn">
                                Join
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>


<a class="logout-btn" href="/web_src/classes/Login/Logout.php">Logout</a>

</body>
</html>