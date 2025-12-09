<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../data_src/api/includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: /web_src/classes/Login/login.php");
    exit();
}

/* User Info */
$users_id = $_SESSION['users_id'];
$stmt = $connection->prepare("SELECT username, wins, losses FROM users WHERE usersID = ?");
$stmt->bind_param("i", $users_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* Stock info */
$stmt = $connection->prepare("SELECT us.ticker FROM users_stocks us WHERE us.usersID = ?");
$stmt->bind_param("i", $users_id);
$stmt->execute();
$stocksResult = $stmt->get_result();

$userStocks = [];
while ($row = $stocksResult->fetch_assoc()) {
    $userStocks[] = $row['ticker'];
}

/* >>> ADD BUILD CHART DATA HERE <<< */
$chartData = [];
foreach ($userStocks as $ticker) {
    $stmt = $connection->prepare("SELECT start_price FROM Holdings WHERE ticker = ?");
    $stmt->bind_param("s", $ticker);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $base = (float)$row['start_price'];
        $prices = [];
        for ($i=0; $i<3; $i++) {   // now 3 days
            $base += rand(-5,5);   // fake random walk
            $prices[] = $base;
        }
        $chartData[] = [
            'ticker' => $ticker,
            'dates' => ["Day 1","Day 2","Day 3"], // 3 days
            'prices' => $prices
        ];
    }
}

/* Handle Save My Stock List submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stocks'])) {
    $del = $connection->prepare("DELETE FROM users_stocks WHERE usersID = ?");
    $del->bind_param("i", $users_id);
    $del->execute();

    $ins = $connection->prepare("INSERT INTO users_stocks (usersID, ticker) VALUES (?, ?)");
    foreach ($_POST['stocks'] as $ticker) {
        $ins->bind_param("is", $users_id, $ticker);
        $ins->execute();
    }
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

/* If in a league, fetch its name */
$currentLeagueName = null;
if ($userLeague !== null) {
    $stmt = $connection->prepare("SELECT leagueName FROM League WHERE leagueID = ?");
    $stmt->bind_param("i", $userLeague);
    $stmt->execute();
    $leagueRes = $stmt->get_result();
    if ($leagueRow = $leagueRes->fetch_assoc()) {
        $currentLeagueName = $leagueRow['leagueName'];
    }
}

/* Fetch all leagues if user is NOT in one */
$availableLeagues = [];
if ($userLeague === null) {
    $sql = "SELECT leagueID, leagueName FROM League";
    $res = $connection->query($sql);
    while ($row = $res->fetch_assoc()) {
        $availableLeagues[] = $row;
    }
}

/* Handle Join League */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_league'])) {
    $leagueID = intval($_POST['join_league']);
    $stmt = $connection->prepare("UPDATE users SET leagueID = ? WHERE usersID = ?");
    $stmt->bind_param("ii", $leagueID, $users_id);
    $stmt->execute();
    header("Location: profile.php");
    exit();
}

/* Profile image */
function setProfileImage($users_id) {
    $imageExtension = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $baseImagePath = $_SERVER['DOCUMENT_ROOT'] . '/web_src/Images/userImages/';
    $imageUrl = '/web_src/Images/userImages/missingUser.png';
    foreach ($imageExtension as $ext) {
        $path = $baseImagePath . 'user_' . $users_id . '.' . $ext;
        if (file_exists($path)) {
            $imageUrl = '/web_src/Images/userImages/user_' . $users_id . '.' . $ext;
            break;
        }
    }
    return $imageUrl;
}
$profileImage = setProfileImage($users_id);
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
    <div class="site-title-container">
        <h1 class="site-title">Fantasy Stocks</h1>
    </div>
    <nav class="site-nav">
        <ul class="site-nav-list">
            <li><a href="index.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="leagues.php">League</a></li>
            <li><a href="matchup.php">Matchup</a></li>
            <li><a href="stocks.php">Stocks</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
</header>

<h2 class="welcome-title">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>

<div class="profile-section">
    <!-- Profile image + upload stacked -->
    <div class="profile-left">
        <div class="profile-img" style="background-image: url('<?php echo $profileImage ?>')"></div>
        <form action="classes/uploadImage.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <label for="fileToUpload" class="upload-btn">Upload Image</label>
            <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" onchange="this.form.submit()">
        </form>
    </div>

    <!-- Stats -->
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
                <?php echo empty($userStocks) ? "No stocks yet" : implode(", ", array_map('htmlspecialchars', $userStocks)); ?>
            </div>
        </div>
    </div>
</div>

<!-- League section ABOVE stocks list -->
<div class="league-browser">
    <?php if ($userLeague === null): ?>
        <h2 class="league-browser-title">Join a League</h2>
        <?php if (empty($availableLeagues)): ?>
            <p>No active leagues available right now.</p>
        <?php else: ?>
            <ul class="league-list">
                <?php foreach ($availableLeagues as $lg): ?>
                <li class="league-item">
                    <span class="league-name"><?php echo htmlspecialchars($lg['leagueName']); ?></span>
                    <form method="POST" class="league-form">
                        <button type="submit" name="join_league" value="<?php echo $lg['leagueID']; ?>" class="join-btn">Join</button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php else: ?>
        <h2 class="league-browser-title">Your League</h2>
        <p>You are currently in: <strong><?php echo htmlspecialchars($currentLeagueName); ?></strong></p>
    <?php endif; ?>
</div>

<!-- Stocks list comes AFTER league browser -->
<div class="stocks-container">
    <form action="profile.php" method="POST" class="stock-picker-form">
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
                    echo '<li class="stock-item"><label>';
                    echo '<input type="checkbox" name="stocks[]" value="'. $ticker .'"> ';
                    echo $ticker . ' – ' . $name . ' (' . $sector . ') – $' . $price;
                    echo '</label></li>';
                }
            } else {
                echo '<p>No stocks found</p>';
            }
            ?>
        </ul>
        <button type="submit" class="save-stocks-btn">Save My Stock List</button>
    </form>
</div>
<!-- Chart section -->
<div class="profile-section">
    <!-- Left side: profile + stats -->
    <div class="profile-left">
        <!-- profile image + upload -->
        <!-- stats + stocks container -->
    </div>

    <!-- Right side: Chart -->
    <div class="profile-chart">
        <canvas id="userStocksChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = <?php echo json_encode($chartData); ?>;

const ctx = document.getElementById('userStocksChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.length ? chartData[0].dates : [],
        datasets: chartData.map(stock => ({
            label: stock.ticker,
            data: stock.prices,
            borderColor: '#' + Math.floor(Math.random()*16777215).toString(16),
            backgroundColor: '#' + Math.floor(Math.random()*16777215).toString(16),
            fill: false
        }))
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: '3‑Day Price Changes (Selected Stocks)' },
            legend: {
                labels: {
                    usePointStyle: true,
                    pointStyle: 'rectRounded',
                    boxWidth: 20,
                    boxHeight: 20
                }
            }
        },
        scales: {
            x: { title: { display: true, text: 'Day' } },
            y: { title: { display: true, text: 'Price ($)' } }
        }
    }
});

</script>

<div class="logout-container">
  <a class="logout-btn" href="/web_src/classes/Login/Logout.php">Logout</a>
</div>
</body>
</html>
`