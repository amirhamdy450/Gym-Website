<?php
  
if (isset($_GET['filled']) && $_GET['filled'] != 'true'){
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_SESSION["first_name"] . " " . $_SESSION["last_name"];
  $bday = $_SESSION["birthday"];
  $gender = $_SESSION["gender"];
  $email = $_SESSION["email"];
  $address = $_SESSION["address"];
  $password = $_SESSION["password"];
  $cpassword = $_SESSION["password_confirmation"];

  $current_time = date('Y-m-d');


  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.php?error=Incorrect Email Format !");
    exit();
  }

  $con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server:( ");
  $db = mysqli_select_db($con, "gym") or die("Error Can't connect to Database !");
  $Insert_User="INSERT INTO instructor(Ins_Name,Address,Email,Password,BirthDate,Gender) VALUE('$name','$address','$email','$password','$bday','$gender')";

  $sql_check = mysqli_query($con, "SELECT * FROM instructor WHERE Email='$email'");
  if (mysqli_num_rows($sql_check) > 0) {
    header("Location: signup.php?error=Email Already Exists !");
    exit();
  }

  $sql_check = mysqli_query($con, "SELECT * FROM instructor WHERE Email='$email'");
  if (mysqli_num_rows($sql_check) > 0) {
    header("Location: signup.php?error=Email already in use !");
    exit();
  }




    if (!preg_match('/[A-Za-z]/', $name)) {
      header("Location: signup.php?error=Your Username Is Not Valid!&msg=Alphabets Only Allowed");
      exit();

    }


    if (strlen($name) < 6) {
      header("Location: signup.php?error=Your Username Is Not Valid!&msg=Your Name Must Have 6 Characters at least !");
      exit();

    }

 
  

  if ($password !== $cpassword) {
    header("Location: signup.php?error=Passwords Unmatched !");
    exit();
  }
  
  else{
    $Password = password_hash($password, PASSWORD_DEFAULT); 

    $result=mysqli_query($con,$Insert_User) or die(mysqli_error($con));

 if($result==true){

     echo "<h1>Success !</h1>";

 }

 else{
    die("Error:".mysqli_errno($con));
 }

}

}
}
?>