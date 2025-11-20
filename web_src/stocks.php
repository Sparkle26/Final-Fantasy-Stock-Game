<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../data_src/api/includes/db_connect.php";
session_start();
?>

<!DOCTYPE html>
<html>
    <header class="site-header">
  <h1 class="site-title">Fantasy Stocks</h1>
  <nav class = "site-nav">
  <ul>
    <li><a href="index.html">Home</a></li>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="leagues.php">League</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="stocks.php">Stocks</a></li>
  </ul>
</header>

<head> 
    <title>Login</title>
    <link rel="stylesheet" href="/stylesheets/styles.css">
    <style>
        .stocks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .stocks-table th, .stocks-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .stocks-table th {
            background-color: #f2f2f2;
        }
        .stocks-container {
            max-width: 900px;
            margin: auto;
        }
    </style>
</head>
<body>
    <h1>Stocks in the NASDAQ 100 available to Players</h1>
    <div class="stocks-container">
        <table class="stocks-table">
            <thead>
                <tr>
                    <th>Ticker</th>
                    <th>Name</th>
                    <th>Sector</th>
                    <th>Start Price of this Week</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to get all stocks
                $sql = "SELECT h.ticker, h.st_name, s.sectorName, h.start_price 
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
                        echo "<td>$" . number_format($row['start_price'], 2) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No stocks found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
