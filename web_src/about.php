<?php
session_start();
require_once "../data_src/api/includes/db_connect.php";
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

<header class="site-header">
    <div class="site-title-container">
        <h1 class="site-title">Fantasy Stocks</h1>
    </div>

    <nav class="site-nav">
        <ul class="site-nav-list">
            <li><a href="index.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
</header>

<main class="about-main">
    <section class="about-section">
        <h2>WHO WE ARE</h2>

        <p>
            Welcome to the Fantasy Stock Game — a project built by a group of students 
            passionate about combining finance, competition, and web development. 
            Our goal is to bring the excitement <br> of fantasy sports into the world of investing.
        </p>

        <p>
            Each member of our team contributed <br> unique skills — from backend development <br> and data scraping 
            to frontend design and user <br> experience. This project represents our shared interest
            in technology and markets.
        </p>

    <section class="card-section">
        <div class="card-container">

            <div class="card" 
                style="--image: url('/web_src/Images/_ (1).jpeg'); 
                       --angle: -5deg; --x: 5%; --y: 15%; 
                       --caption: 'Photo of us working'"></div>

            <div class="card" 
                style="--image: url('/web_src/Images/01aa16e441c66aafc86798964bf6649a.jpg'); 
                       --angle: -1deg; --x: -10%; --y: -20%; 
                       --caption: 'Photo of some computer screens'"></div>

            <div class="card" 
                style="--image: url('/web_src/Images/170803.jpg'); 
                       --angle: -4deg; --x: -20%; --y: 5%; 
                       --caption: 'Our poster'"></div>

            <div class="card" 
                style="--image: url('/web_src/Images/Barbie dreamhouse✭.jpg'); 
                       --angle: 7deg; --x: 10%; --y: -7%; 
                       --caption: 'Us mock presenting maybe'"></div>

        </div>
        <hr class="hr">

        <br>
    </section>

</main>
<hr class="hr">
<main class="about-team">
        <h2>Meet the Team</h2>
        <div class="aya-image"></div>
        <div class="ryder-image"></div>
        <div class="paul-image"></div>
    <section class="team-links">
        <ul>
            <li><a href="general/aboutUs/Aya.php">Aya</a></li>
            <li><a href="general/aboutUs/Paul.php">Paul</a></li>
            <li><a href="general/aboutUs/Ryder.php">Ryder</a></li>
        </ul>
    </section>

</main>

<footer>
    <p>&copy; <?= date("Y"); ?> Fantasy Stock Game Team. All rights reserved.</p>
</footer>

</body>
</html>
