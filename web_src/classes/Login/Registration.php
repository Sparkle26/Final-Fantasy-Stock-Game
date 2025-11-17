<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../stylesheets/styles.css">
</head>

<body>
    <section id="register">
        <div id="welcome-text">Register</div>
        <div id="basicContainer">
            <?php
if (isset($_GET['error'])) {
    $msg = '';
    switch ($_GET['error']) {
        case 'missing':
            $msg = "Please fill in all fields.";
            break;
        case 'nomatch':
            $msg = "Passwords do not match.";
            break;
        case 'taken':
            $msg = "Username already taken.";
            break;
    }
    if ($msg) echo "<p style='color:red; font-weight:bold;'>$msg</p>";
}

if (isset($_GET['success']) && $_GET['success'] === 'registered') {
    echo "<p style='color:green; font-weight:bold;'>Registration successful! You can log in now.</p>";
}
?>

            <form action="../../../data_src/api/Login/registration_jawn.php" method="post">
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
                <input type="submit" value="Register" class="submit-button">
            </form>

            <a class="nav-link" href="Login.php">Back to Login</a>
        </div>
    </section>
</body>
</html>
