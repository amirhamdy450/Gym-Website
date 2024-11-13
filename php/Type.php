<?php
/* verification of the user input */
if ($_SERVER['REQUEST_METHOD'] === 'POST') { /* For security reasons , only enter when it is by POST method */
  $type = $_POST["type"];   /* gets the value of type from the html form  */
  $redirectUrl = "";


  if ( $type == "Member") {    
    $redirectUrl = "signup.php?filled=false";   /* sets the variable to the url of the member signup */
      /* filled=false means that the signup textfields are still empty */
    header("Location: $redirectUrl"); /* this redirects it to the variable that have the URL */
    exit();
  }
  
 /* same logic */
  if ( $type == "Instructor") {
    $redirectUrl = "ins_signup2.php?filled=false";  /* another URL to another page  */

    header("Location: $redirectUrl");
    exit();
  }
  
  



}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="/projects/Gym2/CSS/signup.css">
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
    <h2>Sign Up</h2>
    <form method="POST" action="">
      
  
      <div class="form-group">


      <div class="form-group" id="type">  <!-- here we made an id to retrieve it later from the verification above -->
        <label for="type">Register As</label>
        <select  name="type" required>
          <option value="">--None--</option>
          <option value="Instructor">Instructor</option>
          <option value="Member">Member</option>

        </select>
      </div>
  
      
  
      <div class="form-group">
        <button type="submit">Next</button>
        
        <div class="login">
          <span>Already have an account? <a href="Login.php">Log in</a></span>
        </div>
  
      </div>
    </form>
  </section>


</body>
</html>