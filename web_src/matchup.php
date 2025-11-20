<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../data_src/api/includes/db_connect.php";

// Get leagueID (from GET or session)
$leagueID = isset($_GET['leagueID']) ? intval($_GET['leagueID']) : 1;

$sql = "SELECT usersID, username FROM users WHERE leagueID = ?";
$stmt = $connection->prepare($sql);
if (!$stmt) {
    die("Prepare failed: (" . $connection->errno . ") " . $connection->error);
}

$stmt->bind_param("i", $leagueID);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while($row = $result->fetch_assoc()){
    $users[] = $row;
}
$stmt->close();

if(empty($users)){
    die("No users in this league.");
}

// ---------------------------
// Step 1: Calculate rounds and BYEs
$numUsers = count($users);
$numRounds = ceil(log($numUsers, 2));
$totalSlots = pow(2, $numRounds);
$numByes = $totalSlots - $numUsers;

// Add BYEs (null users)
for($i=0; $i<$numByes; $i++){
    $users[] = null;
}

// Shuffle for random pairing
shuffle($users);

// ---------------------------
// Step 2: Generate bracket
$bracket = [];

// Round 1
$roundMatches = [];
for($i = 0; $i < count($users); $i += 2){
    $roundMatches[] = [
        'user_1' => $users[$i],
        'user_2' => $users[$i+1]
    ];
}
$bracket[1] = $roundMatches;

// Subsequent rounds (placeholders)
for($r=2; $r<=$numRounds; $r++){
    $matchesInRound = pow(2, $numRounds-$r);
    $roundMatches = [];
    for($m=0; $m<$matchesInRound; $m++){
        $roundMatches[] = ['user_1'=>null, 'user_2'=>null];
    }
    $bracket[$r] = $roundMatches;
}
?>

<!DOCTYPE html>
<html>
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

<head> 
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="stylesheets/styles.css" />
  <link rel="stylesheet" href="stylesheets/matchups.css" />
  <title>Stocks!</title>
</head>

<main>
    <h1 class="page-title">League Bracket</h1>

    <div class="bracket-container">
    <?php foreach ($bracket as $roundNum => $matches): ?>
        <div class="round">
            <h2>Round <?php echo $roundNum; ?></h2>
            <?php foreach ($matches as $match): ?>
                <div class="match">
                    <div class="player">
                        <?php 
                            echo $match['user_1'] 
                                ? htmlspecialchars($match['user_1']['username']) 
                                : 'BYE'; 
                        ?>
                    </div>
                    <div class="vs">vs</div>
                    <div class="player">
                        <?php 
                            echo $match['user_2'] 
                                ? htmlspecialchars($match['user_2']['username']) 
                                : 'BYE'; 
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
</main>

</body>
</html>
