<?php
/* An internet session refers to the period of time during which a user is actively connected to the internet */
session_start();  /* starting a new session for the user */
/* gets the email & password from the login form */

$Email = $_POST["Email"];
$Password= $_POST["Password"];
$save_usr=$_POST["Remember_me"];
$con = mysqli_connect("localhost","root","") or die ("Error: can't connect to server:( "); /* standard syntax for connecting to the localhost */
$db =  mysqli_select_db($con,"gym") or die ("Error Can't connect to Database !");  /* connecting to the database called gym */
/* next type the sql quey and save it in a special sql string  variable */
$sql_check=mysqli_query($con , "SELECT * FROM trainee WHERE Email='$Email' And Password='$Password' "); /* checks for the rows that have identical values to what the
user have typed in the form , here the above "And" serves as a logical operator to combine the ceredentials so that both the email & password must match a specific user*/

if(!empty($_POST["Email"]) and !empty($_POST["Password"])){  /*first the variables from the form must not be empty  */
/* we will execute an sql method that will return the no. of rows  of the variable that contains 
our query we declared above*/
    
    if(mysqli_num_rows($sql_check) > 0){ /* if rows returned from our query are more than zero
    then we found a match in our datbase with identical email & password */
    /* the session variables can be used anywhere in our project as long as the session is still active
    unlike the Post & Get which change on each redirect */
   
    $_SESSION["Email"]=$Email;
    $_SESSION["Password"]=$Password;
  /*$sql=mysqli_query($con , "SELECT * FROM trainee WHERE Email='$Email' And Password='$Password' ");  */   
  
 /*fetch_assoc Fetches one row of data from the result set and returns it as an associative array */
    $row = $sql_check->fetch_assoc(); /* row now is an array */
    /* we will set the session variables for the data we will need later in project */
    $_SESSION["id"]=$row["Tr_ID"];
    $_SESSION["name"]=$row["Tr_Name"];
    $_SESSION["Date"]=$row["DateJoined"];   
    $_SESSION["Membership"]=$row["Membership_Level"];
    $_SESSION["img"]=$row["profile_img"];
    $_SESSION["birthday"]=$row["Birth_Date"];
   /* now we return to the homepage and the user is logged in */
   if($save_usr=="saveme"){  /* this checks if the user wants us to remember him */
     session_regenerate_id(true);    /* this is a security function that will regenerat session id to avoid Session Fixation and session hijacking attacks  */
    $session_id = session_id();         /* we created this value and given it the session_id that was just regenerated  */
    setcookie("active_session", $session_id, time() + (86400 * 30), "/"); /* we set the cookie with the session id value and expiration date after 30  days */
    
    }
    header("Location:Home.php");  
    exit();
    }
    
    
    if(!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
    header("Location: Login.php?error=Incorrect Email Format !");
    }



    /* this special condition is when an admin ceredentials has been set
    and we redirect the user to the admin portal */
    if($Email =="admin" && $Password=="admin"){
    header("Location: ../Admin/Admin.php");
    }

   
    /* no matching ceredentials in our database */
    else{
    header("Location: Login.php?error=Incorrect Email or Password !");
    exit();
    
    }

}




?>
