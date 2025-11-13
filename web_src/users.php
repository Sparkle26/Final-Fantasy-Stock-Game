<?php
session_start();
require_once "db_connect.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /web_src/classes/Login/Login.php");
    exit();
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, wins, losses FROM user WHERE userID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>

    <p><strong>Wins:</strong> <?php echo $user['wins']; ?></p>
    <p><strong>Losses:</strong> <?php echo $user['losses']; ?></p>

    <a href="logout.php">Logout</a>
</body>
</html>
