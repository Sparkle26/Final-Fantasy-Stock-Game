<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../data_src/api/includes/db_connect.php';
require_once 'classes/NavBar.php';

//Redirect if not logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: /web_src/classes/Login/Login.php");
  exit();
}

// Fetch user info
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
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <header class="site-header">
  <h1 class="site-title">Fantasy Stocks</h1>
  <nav class = "site-nav">
  <ul>
    <li><a href="index.html">Home</a></li>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="about.php">About</a></li>
  </ul>
</header>
    <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>

    <p><strong>Wins:</strong> <?php echo $user['wins']; ?></p>
    <p><strong>Losses:</strong> <?php echo $user['losses']; ?></p>

    <a href="/web_src/classes/Login/Logout.php">Logout</a>
</body>
</html>
