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
  <link rel="stylesheet" type="text/css" href="/Gym2/CSS/programs.css">
  <script type="text/javascript" src="cart.js"> </script>

</head>
<body>
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
  <div class="title">
  <h1>Our Suplements</h1> <hr>
  </div>

  <?php 
          $con = mysqli_connect("localhost","root","") or die ("Error: can't connect to server:( ");
          $db =  mysqli_select_db($con,"gym") or die ("Error Can't connect to Database !");
          $sql = 'SELECT * FROM supplement';
          $result = mysqli_query($con, $sql);
          // Check if the query was successful
          if (!$result) {
         die('Error: ' . mysqli_error($con));
         }

         // Store the data in the $instructor variable
         $Supplement = mysqli_fetch_all($result, MYSQLI_ASSOC);
          ?>


  <section class="shop">
  <?php foreach ($Supplement as $sp): ?>

  <section class="product-container">
        <div class="program">
            <img src="product1.jpg" alt="Product 1">
            <h2><?php echo $sp['S_Name']; ?></h2>
            <h5> <?php echo $sp['S_Type']?> Supplement</h5>
             <hr>
            <div class="pr_price"><p> <?php echo $sp['S_Price']; ?> EG </p></div>
            <button>View Details</button>
            <button onclick="counter()">Add to Cart</button>
        </div>
  </section>
  <?php endforeach; ?>


  </section>
  
  </body>