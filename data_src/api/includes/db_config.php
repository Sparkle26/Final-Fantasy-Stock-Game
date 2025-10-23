<?php
// File for storing database connection settings

global $host, $database, $dbUsername, $dbPassword;
// If running locally, you'll likely have HTTP_HOST 'localhost' or '127.0.0.1'.
// We assume local dev should connect to local DB; adjust as needed.
if (isset($_SERVER["HTTP_HOST"]) && (strpos($_SERVER["HTTP_HOST"], "127.0.0.1") !== false || strpos($_SERVER["HTTP_HOST"], "localhost") !== false)) {
    $host = "127.0.0.1"; // local DB host — change if you run a different local DB
    $database = "u413142534_fantasydb";
    $dbUsername = "u413142534_fantasy";
    $dbPassword = "StocksR0ck!";
} else {
    // Production settings (these values look suspect - FTP URL removed for mysqli host)
    $host = "etowndb.com";
    $database = "u413142534_fantasy";
    $dbUsername = "u413142534_fantasy";
    $dbPassword = "LetsPL@y4Fun!!";
}

?>