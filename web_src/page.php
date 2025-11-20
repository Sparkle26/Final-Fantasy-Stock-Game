<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Page</title>
    <link rel="stylesheet" href="styles.css">

<?php
// The URL that returns JSON data for rooms
$url = "https://fantasy.etowndb.com/";
// Get the JSON data as a string
$jsonString = @file_get_contents($url);
if ($jsonString === false) {
    $scores = null;
    // provide a visible debug message so page isn't blank
    echo "<div style='color:yellow;background:#222;padding:10px;'>Could not fetch data from $url — check network or URL.</div>";
} else {
    // Convert JSON string to PHP array
    $scores = json_decode($jsonString, true);
}
// echo "<pre>";
// print_r($scores);
// echo "</pre>";

echo "<div style='float:right;margin-right:20px;'>";
echo "<TABLE style=''>";
echo "<tr><td>Demo row</td></tr>";
echo "</TABLE>";
echo "</div>"; // close float:right div

// If we have scores, show a small dump for debugging
if ($scores !== null) {
    echo "<pre style='color:#000;background:#fff;padding:10px;'>" . htmlspecialchars(print_r($scores, true)) . "</pre>";
}
?>
</body>
</html>