<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../data_src/api/includes/db_connect.php";
session_start();
?>

<!DOCTYPE html>
<header class="site-header">
    <div class="site-title-container">
        <h1 class="site-title">Fantasy Stocks</h1>
    </div>

    <nav class="site-nav">
        <ul class="site-nav-list">
            <li><a href="index.html">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="leagues.php">League</a></li>
            <li><a href="stocks.php">Stocks</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
</header>

<head> 
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="stylesheets/styles.css" />
  <link rel="stylesheet" href="stylesheets/stocks.css" />
  <title>Stocks!</title>
</head>
<body>
    <h1>Stocks in the NASDAQ 100 available to Players</h1>
    
    <div class="stocks-list-container">
        <div class="stocks-table-wrapper">
            <table class="stocks-table">
                <thead>
                    <tr>
                        <th>Ticker</th>
                        <th>Name</th>
                        <th>Sector</th>
                        <th>Current Price of the Stocks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to get all stocks
                    $sql = "SELECT h.ticker, h.st_name, s.sectorName, h.curr_price 
                            FROM Holdings h
                            JOIN Sector s ON h.index = s.index";
                    $result = $connection->query($sql);

                    if ($result->num_rows > 0) {
                        // Loop through each row and display it
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['ticker']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['st_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sectorName']) . "</td>";
                            echo "<td>$" . number_format($row['curr_price'], 2) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No stocks found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
