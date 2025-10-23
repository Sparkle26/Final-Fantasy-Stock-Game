<?php
// File for connecting to the database

require_once __DIR__ . '/db_config.php';

// Create database connection
$connection = new mysqli($host, $dbUsername, $dbPassword, $database);

// Check if the connection was successful
if ($connection->connect_error) {
    // Use error message that won't break rendered HTML
    die("Connection failed: " . htmlspecialchars($connection->connect_error));
}

?>