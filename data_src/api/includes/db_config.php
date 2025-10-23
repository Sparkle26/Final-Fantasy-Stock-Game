<!-- File for storing database connection settings  -->
 <?php


global $host, $database, $dbUsername, $dbPassword;
if($_SERVER["HTTP_HOST"]=="127.0.0.1" || $_SERVER["HTTP_HOST"]=="localhost"){
    $host = "srv557.hstgr.io";
    $database = "u413142534_fantasydb";
    $dbUsername = "u413142534_fantasy";
    $dbPassword = "StocksR0ck!";
}else{
    $host = "srv557.hstgr.io";
    $database = "u413142534.fantasy";
    $dbUsername = "u413142534.fantasy";
    $dbPassword = "LetsPL@y4Fun!!";
}


?>