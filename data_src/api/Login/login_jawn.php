<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

// Ensure POST fields exist
if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: ../../../web_src/classes/Login/Login.php?error=missing");
    exit();
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Prepare statement
$stmt = $connection->prepare("SELECT userID, user_password FROM user WHERE username = ?");
if (!$stmt) {
    die("Statement failed: " . htmlspecialchars($connection->error));
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// No such user
if (!$user) {
    header("Location: ../../../web_src/classes/Login/Login.php?error=invalid");
    exit();
}

// Compare plain text passwords
if ($password !== $user['user_password']) {
    header("Location: ../../../web_src/classes/Login/Login.php?error=invalid");
    exit();
}

// Success
$_SESSION['user_id'] = $user['userID'];
header("Location: ../../../web_src/profile.php");
exit();
?>
