<?php
session_start();
require_once "../data_src/api/includes/db_connect.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: classes/Login/Login.php");
    exit();
}

// Fetch league info
$stmt = $pdo->prepare("
                        SELECT u.leagueID, u.username, u.wins, u.losses, l.league_name
                        FROM user u
                        JOIN league l ON u.leagueID = l.leagueID
                        ORDER BY u.wins DESC
                    ");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>League Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php if (!empty($users)): ?>
        <h2>Welcome to <?php echo htmlspecialchars($users[0]['league_name']); ?>!</h2>
    <?php else: ?>
        <h2>No users found in the league.</h2>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</body>
</html>