<?php

  /* Verification of Crendentials */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  /* will only proceed if cerendentials are passed using POST for security */
  
  /* retrieve the parameters from the URL header and put them in local variables to verify them */
  $name = $_POST["first_name"] . " " . $_POST["last_name"];
  $bday = $_POST["birthday"];
  $gender = $_POST["gender"];
  $email = $_POST["email"];
  $membership = $_POST["membership_level"];
  $password = $_POST["password"];
  $cpassword = $_POST["password_confirmation"];

  $current_time = date('Y-m-d');  /* builtin method to get the current date in this format */


  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {   /* builtin method to */
    header("Location: signup.php?error=Incorrect Email Format !");  /* redirects back to signup.php with the error type  */
    exit();     
  }

  $con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server:( "); /* standard sql syntax to connect to the our local host */
  $db = mysqli_select_db($con, "gym") or die("Error Can't connect to Database !");  /* connect to a database called gym that we created */

  /* type the sql query that will execute if verification is successful
  and save it in a  string  variable */
  $Insert_User="INSERT INTO trainee(Tr_Name,Email,Password,Birth_Date,Gender,DateJoined,Membership_Level) VALUE('$name','$email','$password','$bday','$gender','$current_time','$membership')";
  
  /* next type the sql query and save it in a special sql string  variable */
  $sql_check = mysqli_query($con, "SELECT * FROM trainee WHERE Email='$email'");
   /* builtin function that returns the number of rows given the query*/
  if (mysqli_num_rows($sql_check) > 0) {    /* if there is more than one row with this email address then this email logged in before */
    header("Location: signup.php?error=Email Already Exists !");
    exit();
  }



    /* Preg match uses specific patterns to verify textfields */

    if (!preg_match('/[A-Za-z]/', $name)) {
      header("Location: signup.php?error=Your Username Is Not Valid!&msg=Alphabets Only Allowed");
      exit();

    }

   /* checks for the name length */
    if (strlen($name) < 6) {
      header("Location: signup.php?error=Your Username Is Not Valid!&msg=Your Name Must Have 6 Characters at least !");
      exit();

    }

 
   /* see if passwords does not match */  

  if ($password !== $cpassword) {
    header("Location: signup.php?error=Passwords Unmatched !");
    exit();
  }
  

  /* this else is when there verification is successful */
  else{
    /* hashing encrypts the user password , here we used a builtin method  */
    $Password = password_hash($password, PASSWORD_DEFAULT); 
    /* adding the new user to the database */
    $result=mysqli_query($con,$Insert_User) or die(mysqli_error($con));

 if($result==true){

     echo "<h1>Success !</h1>";

 }
 /* if filled is empty */
 else{
    die("Error:".mysqli_errno($con));
 }

}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="google-signin-client_id" content="1007872885686-0vlndmia2pmoub2mn9ov3u0cep98uffs.apps.googleusercontent.com">  <!-- include the client id we created at google cloud to use google sign-in -->
  <title>Sign Up</title>
  <link rel="stylesheet" href="/Gym2/CSS/signup.css">
  <link rel="stylesheet" href="/Gym2/CSS/Error.css">
  <script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
  <script src="Gbutton.js"></script>     
</head>
<body>
  <nav>
    <div class="logo">
      <a href="Home.php">Epix Gym</a>
    </div>
    <ul class="nav-links">
      <li><a href="Home.php">Home</a></li>
      <li><a href="Home.php #toplans">Plans</a></li>
      <li><a href="Home.php #toabout">About Us</a></li>
      <li><a href="Home.php #tocontact">Contact Us</a></li>
      <li><a href="Login.php">Login</a></li>
    </ul>
  </nav>
  <section class="signup">

  <?php if (isset($_GET['error'])) { ?> <!-- it checks in the URL header if the error variable is not empty  -->
   
    <!-- error is declared in the verification above if there is an error  -->

  <p class="error">
 
  <strong>
  <?php
  /* we will fetch the error string from above and display it in our html */
  echo $_GET['error']."<br>"; ?> </strong>
 


</p>

<?php } ?>




    <h2>Sign Up</h2>
    <form method="POST" action="">  <!--the action is empty because verification is above  -->
      
  
      <div class="form-group">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" required>
      </div>
  
      <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" required>
      </div>
  

      <div class="form-group">
        <label for="birthday">Birthday</label>
        <input type="date" id="birthday" name="birthday" required>
      </div>
  
      <div class="form-group" id="select">
        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
          <option value="">Select Gender</option>
          <option value="M">Male</option>
          <option value="F">Female</option>
        </select>
      </div>
  
      <div class="form-group" id="select">
        <label for="membership_level">Membership Level</label>
        <select  name="membership_level" required>
          <option value="">Select Membership Level</option>
          <option value="BRONZE">Bronze</option>
          <option value="SILVER">Silver</option>
          <option value="GOLD">Gold</option>
        </select>
      </div>

  
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
  
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
  
      <div class="form-group">
        <label for="password-confirm">Confirm Password</label>
        <input type="password" id="password-confirm" name="password_confirmation" required>
      </div>
  
      <div class="form-group">
        <button type="submit">Sign Up</button>
        <div  class="g-login" id="gbutton"  data-onsuccess="onSignIn"></div>
        <div class="login">
          <span>Already have an account? <a href="Login.php">Log in</a></span>
        </div>
  
      </div>
    </form>
  </section>

   <footer>
    <p>&copy; 2023 Epix Gym. All rights reserved.</p>
  </footer> 
</body>
</html>