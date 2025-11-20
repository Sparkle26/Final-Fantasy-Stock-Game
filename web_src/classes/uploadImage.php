<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once "../../data_src/api/includes/db_connect.php";

$userID = $_SESSION["users_id"] ?? null;
$extension = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ext = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
$target_dir = "../../../web_src/Images/userImages/";
$target_file =$target_dir . "user_" . $userID . "." . $ext;
$imageFileType = $ext;
$uploadOk = 1;

// Check If Logged In
if ($userID === null) {
    die("Error: User not found.");
}

// Checks File Size
if ($_FILES["fileToUpload"]["size"] > 5000000) {
    echo "Error: File size exceeds the 5MB limit.";
    $uploadOk = 0;
}

// Checks File Extension
if (!in_array($ext, $extension)) {
    echo "Error: Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.";
    $uploadOk = 0;
}

// Check if file already exists and remove it if does
if (file_exists($target_file)) {
    unlink($target_file);
}

if ($uploadOk === 0) {
    echo "Error: Your file was not uploaded.";
} else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file has been uploaded successfully.";
    } else {
        echo "Error: There was an error uploading your file.";
    }
}

?>