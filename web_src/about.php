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
            <li><a href="leagues.php">League</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
</header>

<section class="about-layout">

    <div class="about-left">
        <h2>WHO WE ARE</h2>
        <p>
            Welcome to the Fantasy Stock Game — a project built by a group of students 
            passionate about combining finance, competition, and web development.
            Our goal is to bring the excitement of fantasy sports into the&nbsp;<br>world of investing.
        </p>

        <p>
            Each member of our team contributed unique skills — from backend development 
            and data scraping to frontend design and user&nbsp; experience. This project 
            represents our shared interest in technology and markets.
        </p>
    </div>

    <div class="about-right">
        <div class="card-wrapper">

            <div class="card"
                style="--image: url('/web_src/Images/_ (1).jpeg'); 
                       --angle: -15deg; --x: 190px; --y: 90px;
                       --caption: 'Photo of us working';">
            </div>

            <div class="card"
                style="--image: url('/web_src/Images/01aa16e441c66aafc86798964bf6649a.jpg'); 
                       --angle: 12deg; --x: -90px; --y: -40px;
                       --caption: 'Computer screens';">
            </div>

            <div class="card"
                style="--image: url('/web_src/Images/170803.jpg'); 
                       --angle: -10deg; --x: -40px; --y: 120px;
                       --caption: 'Our poster';">
            </div>

            <div class="card"
                style="--image: url('/web_src/Images/Barbie dreamhouse✭.jpg'); 
                       --angle: 18deg; --x: 100px; --y: -50px;
                       --caption: 'Presentation moment';">
            </div>

        </div>
    </div>

</section>

<hr class="hr">

<section class="about-team">
    <h3>Meet the Team</h3>
    <div class="team-container">
        <div class="team-member">
            <div class="profile-img aya-image"></div>
            <a href="https://www.linkedin.com/in/aya-zourgani/">Aya</a>
            <p class="team-bio">Hey! I'm Aya, a senior majoring in Graphic Design and Computer Science at Elizabethtown College.
                                I focus on front-end development, creating seamless and visually appealing user experiences.
                                Welcome to our project. I hope you enjoy exploring it as much as we enjoyed building it!</p>
        </div>

        <div class="team-member">
            <div class="profile-img ryder-image"></div>
            <a href="https://www.linkedin.com/in/rpaulus1326/">Ryder</a>
            <p class="team-bio">Hi! I'm a Junior International Business and Information Systems major with concentrations in Data Analytics<br> and Management.
                                I focus on implementing features like login systems and database integration to make our projects functional and efficient.
                                I'm excited to apply my skills in real-world applications. Connect with me on LinkedIn!.</p>
        </div>

        <div class="team-member">
            <div class="profile-img paul-image"></div>
            <a href="https://www.linkedin.com/in/pauldavis05/">Paul</a>
            <p class="team-bio">Hello! My name is Paul, and I am a junior Computer Science major.
                                I worked on the API development and some of the data scraping components
                                to bring functionality and real-world data into <br> our Fantasy Stock Game.</p>
        </div>
    </div>
</section>

<bottom-footer>
    <p>&copy; <?= date("Y"); ?> Fantasy Stock Game Team. All rights reserved.</p>
</bottom-footer>

</body>
</html>
