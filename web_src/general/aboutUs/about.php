<?php
// Optional if you'll add session-based navigation later
// session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Our Team | Fantasy Stock Game</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>About Our Team</h1>
    </header>

    <main class="content">
        <section class="about-section">
            <h2>Who We Are</h2>
            <p>
                Welcome to the <strong>Fantasy Stock Game</strong> — a project built by a group of students 
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
</body>
</html>
