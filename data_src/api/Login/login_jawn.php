<?php
session_start();
require_once '../includes/db_connect.php'; // Make sure this defines $pdo

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT userID, username, password FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['userID'];
        $_SESSION['username'] = $user['username'];

        header("Location: /web_src/users.php");
        exit();
    } else {
        // Invalid login
        header("Location: /web_src/classes/Login/Login.php?error=invalid");
        exit();
    }
} else {
    header("Location: /web_src/classes/Login/Login.php");
    exit();
}
?>