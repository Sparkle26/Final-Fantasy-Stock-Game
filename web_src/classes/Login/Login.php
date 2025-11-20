<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../NavBar.php';
?>

<!DOCTYPE html>
<html>
<header class="site-header">
  <h1 class="site-title">Fantasy Stocks</h1>
  <nav class = "site-nav">
  <ul>
    <li><a href="../../index.html">Home</a></li>
    <li><a href="../../profile.php">Profile</a></li>
    <li><a href="../../leagues.php">League</a></li>
    <li><a href="../../about.php">About</a></li>
  </ul>
</header>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../stylesheets/styles.css">
</head>
        <section id="login">
        <div id="welcome-text">Login</div>
        <div>
        <div id="basicContainer">
            <!-- Form to get login info from user -->


        <form action= "/data_src/api/Login/login_jawn.php" method= "post">
             <input type="text" id="username" name="username" placeholder="Username" required><br>
             <input type="password" id="password" name="password" placeholder="Password" required><br>
             <input type="submit" value="Submit" class="submit-button">
        </form>
            


            <!-- Register button for if not already an admin -->
            <a class="nav-link" href="Registration.php">
             Register
            </a>
        </div>
        </div>
        </section>';
</html>