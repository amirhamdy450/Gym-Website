<?php
session_start();

if (!isset($_FILES["file_img"]) || $_FILES["file_img"]["error"] != UPLOAD_ERR_OK) {
    die("Error in file upload!");
}

$Email = $_SESSION["Email"]; // Ensure this is sanitized or validated
$dir = "D:/APPS/Xampp_20/Server/Gym/Profile_imgs/"; // Make sure this directory exists and ends with a slash

$tmp_file = $_FILES["file_img"]["tmp_name"];
$image_info = getimagesize($tmp_file);

if ($image_info === false) {
    die("Invalid image file.");
}

if (!in_array($image_info[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
    die("Unsupported image format!");
}

$imageType = $image_info[2];
$filename = $dir . uniqid('img_') . image_type_to_extension($imageType);

// Move the uploaded file
$is_moved = move_uploaded_file($tmp_file, $filename);

if (!$is_moved) {
    die("Failed to save image.");
}

// Base64 encode the image
$img_data = base64_encode(file_get_contents($filename));

// Database connection and update
$con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server");
$db = mysqli_select_db($con, "gym") or die("Error Can't connect to Database");

$Insert_img = "UPDATE trainee SET profile_img='" . mysqli_real_escape_string($con, $img_data) . "' WHERE Email= '" . mysqli_real_escape_string($con, $Email) . "'";
$sql_check = mysqli_query($con, $Insert_img) or die(mysqli_error($con));
unlink($filename);     /* deleting the file that we retrieved the data in our $dir variable  */
/* although the best practice is to keep it in our server directory and only add a pointer/reference to it inside our mysql database */
header("Location:Home.php");
exit();
?>
