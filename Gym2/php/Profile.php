<!-- An internet session refers to the period of time during which a user is actively connected to the internet -->
<?php session_start(); /* starting a session  */
 if (!isset($_SESSION["Email"])) {           /* check from the session if the email is set before redirecting to profile */
  header("Location: Home.php");                /* if the email is not set we redirect the user to the homepage */
 } 
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Gym Website</title>
  <link rel="stylesheet" type="text/css" href="/Gym2/CSS/global.css">
  <link rel="stylesheet" type="text/css" href="/Gym2/CSS/nav.css">
  <link rel="stylesheet" type="text/css" href="/Gym2/CSS/Profile.css">

  <script type="text/javascript" src="img_manager.js"> </script>

</head>
<body id="prof" >
  <nav>
    <div class="logo">
      <a href="Home.php">Epix Gym</a>
    </div>
    <ul class="nav-links">
    <li><a href="Home.php">Home</a></li>
      <li><a href="Home.php #toplans">Plans</a></li>
      <li><a href="Home.php #toabout">About Us</a></li>
      <li><a href="Home.php #tocontact">Contact Us</a></li>
</nav>

    <section class="profile" id="script2">
      <h1>User Profile</h1> <hr> <br>
      <div class="profile-info">

        <div class="profile-picture-wrapper">

        <?php   $con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server:( "); /* standard sql syntax to connect to the our local host */
      $db = mysqli_select_db($con, "gym") or die("Error Can't connect to Database !");  /* connect to a database called gym that we created */

      $email=$_SESSION["Email"];
      $sql_check = mysqli_query($con, "SELECT profile_img FROM trainee WHERE Email='$email'");
      $row=mysqli_fetch_assoc($sql_check);
      if (is_null($row["profile_img"])) {    /* if there is more than one row with this email address then this email logged in before */
        
   ?>
          
          <img src="https://static.vecteezy.com/system/resources/previews/001/840/618/original/picture-profile-icon-male-icon-human-or-people-sign-and-symbol-free-vector.jpg" alt="Profile Picture" id="profile-picture-img">

          <?php } else { 

          echo '<img src="data:image/jpeg;base64,' . $row["profile_img"] . '" id="profile-picture-img" alt="Profile picture" >';

          } ?>

          <div class="pic-change">
            
          <input type="file" id="choose" name="file_img" form="img" accept=".jpg, .jpeg, .png, .gif" onchange="img_manager()">   <!-- the form attribute is new in HTML5 that sets a specific id when a form is submitted to retrieve values that are outside the form -->
          
          <!-- the img_manager function in js will help us manipulate the html dynamically when a file is selected --> 
          <div class="icon"><img src="camera.png"></div>
            

            
          <div class="caption">Change Picture</div>

          </div>

        </div>

        <div class="user-details">
          <h2><?= $_SESSION['name'] ?></h2> <br>
          <p><b>Email: </b> <?= $_SESSION['Email'] ?></p>
          <p><b>Date Joined: </b>  <?= $_SESSION['Date'] ?></p>
          <p><b> Personal Instructor: </b> <a href="#" style="color:#f00;"><b>Ahmed Saad</b></a> </p>

          <br>   <br>      
          <div class="form-group">
            <label for="user-birthday">Birthday:</label>
          <?php echo "<p name='user_birthday' id='user-birthday' >".date("Y-m-d",strtotime($_SESSION['birthday']))."</p>" ?>
          </div>
           <br>
          <div class="membership-level">
          <label ><b>Membership Level </b> </label>
           <hr> 
        <p>  <?= $_SESSION['Membership'] ?> </p>
         <hr>  
        </div>
          <div class="change-plan">
            <button type="button" name="change_plan">Change Plan</button>
          </div>
          
          <div class="password-change">
            <h3>Change Password </h3> 
            <hr>
            <form method="post">
              <div class="form-group">
                <label for="current-password">Current Password</label>
                <input type="password" name="current_password" id="current-password">
              </div>
              <div class="form-group">
                <label for="new-password">New Password</label>
                <input type="password" name="new_password" id="new-password">
              </div>

            <button type="submit" name="save_changes" class="btn-save-changes">Save Changes</button>
            </form>
          </div>
        </div>
      </div>

      <footer>
    <p>&copy; 2023 Gym. All rights reserved.</p>
  </footer>
  
    </section>
     
    <div id="script" > </div>



</body>
</html>