<?php

$name = $_POST["first_name"]." ". $_POST["last_name"] ;
$bday = $_POST["birthday"];
$Gender=$_POST["gender"];
$Email = $_POST["Email"];
$membership=$_POST["membership_level"];
$Password= $_POST["password"];
$Cpassword=$_POST["password-confirm"];

$current_time = date('Y-m-d');




$con = mysqli_connect("localhost","root","") or die ("Error: can't connect to server:( ");
$db =  mysqli_select_db($con,"gym") or die ("Error Can't connect to Database !");
$Insert_User="INSERT INTO trainee(Tr_Name,Email,Password,Birth_Date,Gender,DateJoined,Membership_Level) VALUE('$name','$Email','$Password','$bday','$Gender','$current_time','$membership')";
$sql_check=mysqli_query($con , "SELECT * FROM users WHERE Email='$Email'");


if(!filter_var($Email, FILTER_VALIDATE_EMAIL)) {       //check if the email format is correct
    header("Location: signup.php?error=Incorrect Email Format !");
}


if(mysqli_num_rows($sql_check) > 0){
header("Location: signup.php?error=Email Already Exists !");

}


$sql_check=mysqli_query($con , "SELECT * FROM users WHERE U_name='$U_name'");


    if(mysqli_num_rows($sql_check) > 0){
    header("Location: signup.php?error=Username already taken!");

    }



    if (!preg_match('/[A-Za-z]/', $name)) {
        $error_msg = "-Name must contain letters!";
        header("Location: signup.php?error=Your Username Is Not Valid!&msg=$error_msgs");
        exit();

    }


    if (strlen($name) < 6) {
        $error_msg = "-Name must be at least 6 characters";
        header("Location: signup.php?error=Your Username Is Not Valid!&msg=$error_msg");
        exit();

    }





    if ($Password != $Cpassword) {
        header("Location: signup.php?error=Passwords Unmatched!");
        exit();

    }





else{
    $Password = password_hash($Password, PASSWORD_DEFAULT); 

    $result=mysqli_query($con,$Insert_User) or die(mysqli_error($con));

 if($result==true){

     echo "<h1>Success !</h1>";

 }

 else{
    die("Error:".mysqli_errno($con));
 }

}

?>