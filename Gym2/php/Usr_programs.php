<?php session_start(); /* first we need to start the session */
if (isset($_COOKIE['session_id'])) {  /* if there is an existing cookie of a previousely logged-in user */
  session_id($_COOKIE['session_id']); /* set the session id to the previous one */
  session_start();
  // User is logged in
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
  <link rel="stylesheet" type="text/css" href="/Gym2/CSS/usr_programs.css">
  <script type="text/javascript" src="cart.js"> </script>

</head>
<body id="prof">
<nav>
    <div class="logo">
      <a href="Home.php">Epix Gym</a>
    </div>
    <ul class="nav-links">
      <li><a href="#">Home</a></li>
      <li><a href="#toplans">Plans</a></li>
      <li><a href="#toabout">About Us</a></li>
      <li><a href="#tocontact">Contact Us</a></li>
     <!-- checks if email is set in the session  -->
      <?php if (isset($_SESSION["Email"])) { ?> <!-- if the email is set in the session then the user logged in successfully -->
      
    <li class="dropdown">
    <a href="#" class="dropdown-toggle"><?php echo $_SESSION["name"]; ?></a> <!-- retrieve the name associated with the email in the session -->
    <ul class="dropdown-menu">
      <li><a href="Profile.php">View Profile</a></li>
      <li><a href="Logout.php">Logout</a></li>
    </ul>
  </li>
  <li id="cart"><a href=""><img width="30" height="25" src="https://img.icons8.com/ios-filled/50/add-shopping-cart.png" alt="add-shopping-cart"/></a></li>

<?php } else { ?>       <!-- else then user is not logged in --> 
  <li><a href="login.php">Login</a></li>
<?php } ?>


    </ul>
  </nav>


  <body>


  <?php 
          $con = mysqli_connect("localhost","root","") or die ("Error: can't connect to server:( ");
          $db =  mysqli_select_db($con,"gym") or die ("Error Can't connect to Database !");
          $id=$_SESSION["id"];
          $sql = "SELECT Program_Number FROM trainee_take_program WHERE Tr_ID='$id'";
          $result = mysqli_query($con, $sql);
          // Check if the query was successful
          if (!$result) {
         die('Error: ' . mysqli_error($con));
         }

         if(mysqli_num_rows($result) > 0){
          $Pr_ids = mysqli_fetch_all($result, MYSQLI_ASSOC);
      
          foreach($Pr_ids as $pid){
              $current_pid = $pid['Program_Number'];
              $sql = "SELECT * FROM program WHERE Program_Number='$current_pid'"; 
              $result = mysqli_query($con, $sql); // Execute the query for each Program_Number
              if (!$result) {
                  die('Error: ' . mysqli_error($con));
              }
              $Programs = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
   <section class="shop">
  <?php foreach ($Programs as $pr){ ?>
    <h1>Your Programs</h1> <hr>
  <section class="product-container">
        <div class="program">
            <img src="product1.jpg" alt="Product 1">
            <h2><?php echo $pr['Program_Type']; ?></h2>
            <h4>Over  <?php echo $pr['No_Of_Days']?> day(s) period</h4>
            <button>View Details</button>


        </div>
  </section>
  <?php } ?>


  </section>


<?php  }
 } 
 else{ ?>
  <section class="product-container">
        <div class="program">
            <h2>Nothing To Show</h2>

        </div>
  </section>

<?php

}?>




  
  </body>