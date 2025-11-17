<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../includes/db_connect.php'; // same MySQLi connection

if (!isset($_POST['username'], $_POST['password'], $_POST['confirm_password'])) {
    header("Location: ../../../web_src/classes/Login/Registration.php?error=missing");
    exit();
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$confirm = trim($_POST['confirm_password']);

// Check passwords match
if ($password !== $confirm) {
    header("Location: ../../../web_src/classes/Login/Registration.php?error=nomatch");
    exit();
}

// Check if username already exists
$stmt = $connection->prepare("SELECT userID FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: ../../../web_src/classes/Login/Registration.php?error=taken");
    exit();
}

// Insert new user
$stmt = $connection->prepare("INSERT INTO user (username, user_password, wins, losses) VALUES (?, ?, 0, 0)");
$stmt->bind_param("ss", $username, $password);
if ($stmt->execute()) {
    header("Location: ../../../web_src/classes/Login/Login.php?success=registered");
    exit();
} else {
    die("Error creating user: " . htmlspecialchars($stmt->error));
}
?>
