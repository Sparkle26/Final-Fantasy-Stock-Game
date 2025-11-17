<?php
session_start();
require_once "../data_src/api/includes/db_connect.php";

// Optional session-based redirects
/*
if (!isset($_SESSION['user_id'])) {
    header("Location: /web_src/classes/Login/Login.php");
    exit();
}
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Our Team | Fantasy Stock Game</title>

    <link rel="stylesheet" href="stylesheets/about.css">
    <link rel="stylesheet" href="stylesheets/styles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Integrated Header + Navbar -->
<header class="site-header">
    <div class="site-title-container">
        <h1 class="site-title">Fantasy Stocks</h1>
    </div>

<<<<<<< HEAD
        <section class="team-links">
            <h2>Meet the Team</h2>
            <ul>
                <li><a href="general/aboutUs/Aya.php">Aya</a></li>
                <li><a href="general/aboutUs/Paul.php">Paul</a></li>
                <li><a href="general/aboutUS/Ryder.php">Ryder</a></li>
            </ul>
        </section>
    </main>
=======
    <nav class="site-nav">
        <ul class="site-nav-list">
            <li><a href="index.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
</header>

<main class="content">
    <section class="about-section">
        <h2>WHO WE ARE</h2>
        <p>
            Welcome to the Fantasy Stock Game — a project built by a group of students 
            passionate about combining finance, competition, and web development. 
            Our goal is to bring the excitement of fantasy sports into the world of investing.
        </p>
        <p>
            Each member of our team contributed unique skills — from backend development and data scraping 
            to frontend design and user experience. This project represents our shared interest in 
            technology and markets.
        </p>
    </section>

    <section class="team-links">
        <h2>Meet the Team</h2>
        <ul>
            <li><a href="Aya.php">Aya</a></li>
            <li><a href="Paul.php">Paul</a></li>
            <li><a href="Ryder.php">Ryder</a></li>
        </ul>
    </section>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Fantasy Stock Game Team. All rights reserved.</p>
</footer>
>>>>>>> 0d51d04 (fixing photo on home screen and progess on about page)

</body>
</html>
