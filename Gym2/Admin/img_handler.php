<?php


const dir= "D:/APPS/Xampp_20/Server/Gym/tmp/";  /* declaring a constant directory variable to be accessed globally    */


function img_handler($recent_id=NULL){   /* GET the recent id of the latest query in case of an insert , the equal sign gives it a default value if nothing is passed to the fucntion */
    
$url=$_SERVER['PHP_SELF']; /* accessing the http url */
echo $url;
$parse=parse_url($url);   /* parsing it in parse variable */
$page=basename($parse['path']);      /* getting the page that the request was sent from */
parse_str(parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY),$params);  /* accessing the extra parameters coming with the url */

$id=$params['id'];    /* getting the id parameter value */
$action=$params['action'];  /* getting the action parameter */
echo '<p>'.$id.'</p>';

$tmp_file = $_FILES["file_img"]["tmp_name"];

$image_info = getimagesize($tmp_file);

if ($image_info === false) {
    $error = "-1 Invalid image file.";
    return $error;
}

if (!in_array($image_info[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
    die("Unsupported image format!");
}

$imageType = $image_info[2];
$filename = dir.uniqid('img_') . image_type_to_extension($imageType);


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





if(strpos($page,'trainee.php')!==FALSE){
    switch($action){
      case 'add':
        
        $Insert_img = "UPDATE trainee SET profile_img='" . mysqli_real_escape_string($con, $img_data) . "' WHERE Tr_ID= '" . mysqli_real_escape_string($con, $recent_id) . "'";
        $sql_check = mysqli_query($con, $Insert_img) or die(mysqli_error($con));
        unlink($filename);
        break;
      
     case 'edit':
        $Insert_img = "UPDATE trainee SET profile_img='" . mysqli_real_escape_string($con, $img_data) . "' WHERE Tr_ID= '" . mysqli_real_escape_string($con, $id) . "'";
        $sql_check = mysqli_query($con, $Insert_img) or die(mysqli_error($con));
        unlink($filename);



    }
} 


else if(strpos($page,'sup.php')!==FALSE){
    switch($action){
      case 'add':
        
        $Insert_img = "UPDATE supplement SET S_img='" . mysqli_real_escape_string($con, $img_data) . "' WHERE S_ID= '" . mysqli_real_escape_string($con, $recent_id) . "'";
        $sql_check = mysqli_query($con, $Insert_img) or die(mysqli_error($con));
        unlink($filename);
        break;
      
     case 'edit':
        $Insert_img = "UPDATE supplement SET S_img='" . mysqli_real_escape_string($con, $img_data) . "' WHERE S_ID= '" . mysqli_real_escape_string($con, $id) . "'";
        $sql_check = mysqli_query($con, $Insert_img) or die(mysqli_error($con));
        unlink($filename);



    }
} else if(strpos($page,'instructors.php')!==FALSE){
    switch($action){
      case 'add':
        
        $Insert_img = "UPDATE instructor SET Ins_img='" . mysqli_real_escape_string($con, $img_data) . "' WHERE Ins_ID= '" . mysqli_real_escape_string($con, $recent_id) . "'";
        $sql_check = mysqli_query($con, $Insert_img) or die(mysqli_error($con));
        unlink($filename);
        break;
      
     case 'edit':
        $Insert_img = "UPDATE instructor SET Ins_img='" . mysqli_real_escape_string($con, $img_data) . "' WHERE Ins_ID= '" . mysqli_real_escape_string($con, $id) . "'";
        $sql_check = mysqli_query($con, $Insert_img) or die(mysqli_error($con));
        unlink($filename);



    }
} 

    

}







?>