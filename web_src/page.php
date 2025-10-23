<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aya's Chat</title>
    <link rel="stylesheet" href="styles.css">

<?php
// The URL that returns JSON data for rooms
$url = "https://fantasy.etowndb.com/";
// Get the JSON data as a string
$jsonString = file_get_contents($url);
// Convert JSON string to PHP array
$scores = json_decode($jsonString, true);
// echo "<pre>";
// print_r($scores);
// echo "</pre>";

echo "<div style='float:right;margin-right:20px;'>";
echo "<TABLE style=''>";


echo "</TABLE>";
echo "<div>";
?>
</body>
</html>