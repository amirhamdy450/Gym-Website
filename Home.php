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
  <link rel="stylesheet" type="text/css" href="CSS/nav.css">
  <link rel="stylesheet" type="text/css" href="CSS/global.css">
  <link rel="stylesheet" type="text/css" href="CSS/Home.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script type="text/javascript" src="cart.js"> </script>
  <script type="text/javascript" src="cartlist.js"> </script>


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
            <li><a href="Usr_programs.php">View My Programs</a></li>
            <li><a href="Logout.php">Logout</a></li>
          </ul>
        </li>
        <li id="cart"><button id="cart_btn" onclick="openList()"><img width="30" height="25" src="https://img.icons8.com/ios-filled/50/add-shopping-cart.png" alt="add-shopping-cart" /></button></li>





      <?php } else { ?> <!-- else then user is not logged in -->
        <li><a href="php/Login.php">Login</a></li>
      <?php } ?>


    </ul>
  </nav>


  <!-- side cart -->
  <?php
  $con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server:( ");
  $db =  mysqli_select_db($con, "gym") or die("Error Can't connect to Database !");


  ?>


  <div class="sidecart" id="cartlist">
    <div class="cart_content">

      <div class="cart_header">

        <div class="cart-title">
          <h2>Your Cart </h2>
        </div>
      </div>

      <div class="cart_items">

        <?php
        if (!empty($_SESSION["cart"])) {
          $cart = $_SESSION["cart"];
          foreach ($cart as $item):
            $item_id = intval($item['id']); // make sure the product id is an integer
            $sql = "SELECT * FROM program WHERE Program_Number = $item_id";
            $result = mysqli_query($con, $sql);

            if (!$result) {
              die('Error: ' . mysqli_error($con));
            }

            $program = mysqli_fetch_assoc($result); // fetch the product details

        ?>


            <div class="cart_item" id="item-<?php echo $item['id']; ?>"> <!-- give use unique id specific to the product id to allow use later to manipulate items -->

              <div class="remove">
                <span>&times;</span>
              </div>

              <div class="item_img">
                <img src="https://m.media-amazon.com/images/I/61-pD1C6SDL._AC_SL1000_.jpg" alt="fail" />
              </div>

              <div class="item_details">
                <p><?php echo  $program['Program_Type'];  ?></p>
                <strong><?php echo  $program['Price'];   ?></strong>

                <div class="item_qty">
                  <span><a onclick=" RemoveFromCart(<?php echo $item['id']; ?>)">-</a></span>
                  <strong id="qty-<?php echo $item['id']; ?>"> <?php echo $item['quantity'];  ?> </strong>
                  <span><a onclick=" AddToCart('<?php echo $item['id']; ?>', '<?php echo $item['price']; ?>')">+ </a></span>
                </div>

              </div>

            </div>



          <?php
          endforeach;
        } else { ?>
          <p>Your cart is empty!</p>
        <?php } ?>




      </div>

      <div class="cart_btns">

        <div class="total">

          <p>TOTAL:</p>
          <P><span id="total_price">380</span> EG </P>
        </div>

        <button>Checkout</button>

      </div>

    </div>
  </div>




  <header>

    <div class="hero-text">
      <?php if (isset($_SESSION["Email"])) { ?>
        <h1>Welcome back, <?php echo $_SESSION["name"]; ?>!</h1> <!-- retrieve the name of the user associated with the email in the session-->
        <p>Get fit and healthy with our professional trainers and state-of-the-art facilities.</p>
        <a href="programs.php" style="color:#ffff;">
          <button>Browse Programs</button>
        </a>
      <?php } else { ?> <!-- if there is no email in the session then this user is not logged in -->
        <h1>Welcome to Epix Gym</h1>
        <p>Get fit and healthy with our professional trainers and state-of-the-art facilities.</p>

        <a href="signup.php" style="color:#ffff;">
          <button>Join Now</button>
        </a>

        <a href="ins_request.php" style="color:#ffff;">
          <button id="ins_btn">Become An Instructor</button>
        </a>

      <?php } ?>
    </div>

  </header>


  <section class="plans" id="toplans">
    <h2>Our Plans</h2>
    <hr id="title">
    <div class="plan-container">

      <div class="plan">
        <h3><span id="BronzeColor">Bronze</span> Memership Plan</h3><br>
        <hr> <br>
        <div class="PlanContent">

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>Access to our gym facilities 24/7</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>1 body checkup per month</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>4 inivitations per month</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/RedX.svg" alt="">
            <p>personal training sessions per month</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/RedX.svg" alt="">
            <p>Group Sessions per month</p>
          </div>
        
          <div class="PlanBenefit">
            <img src="Imgs/Icons/RedX.svg" alt="">
            <p>Spa Access</p>
          </div>

          <div class="price">
            <p>800 EG / month</p>
          </div>

          <?php if (isset($_SESSION["Membership"]) && $_SESSION["Membership"] == "BRONZE") { ?> <!-- check if membership var is not empty and is Bronze -->
            <!-- displays the membership level of the logged in user by retrieving session variables -->
            <a style="color:#ffff;">
              <button id="subscribed">Subscribed <img src="ok.png" width="13" height="13">
              </button>
            </a>

          <?php } else if (isset($_SESSION["Membership"])) { ?> <!-- if the user have a different membership other than BRONZE -->
            <a href="Profile.php" style="color:#ffff;">
              <button>Change Your Plan</button>
            </a>

          <?php } else { ?> <!-- this means that the header is empty and therefore the user did not log in -->
            <a href="signup.php" style="color:#ffff;">
              <button>Subscribe</button>
            </a>
          <?php } ?>
        </div>
      </div>

      <div class="plan">
        <h3><span id="SilverColor">Silver</span> Memership Plan</h3><br>
        <hr> <br>
        <div class="PlanContent">

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>Access to our gym facilities 24/7</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>2 body checkup per month</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>8 inivitations per month</p>
          </div>

          <div class="PlanBenefit">
           <img src="Imgs/Icons/check.png" alt="">
            <p>2 personal training sessions per month</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>3 Group Sessions per month</p>
          </div>
        
          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>Spa Access</p>
          </div>

          <div class="price">
            <p>1200 EG / month</p>
          </div>
          <?php if (isset($_SESSION["Membership"]) && $_SESSION["Membership"] == "BRONZE") { ?> <!-- check if membership var is not empty and is Bronze -->
            <!-- displays the membership level of the logged in user by retrieving session variables -->
            <a style="color:#ffff;">
              <button id="subscribed">Subscribed <img src="ok.png" width="13" height="13">
              </button>
            </a>

          <?php } else if (isset($_SESSION["Membership"])) { ?> <!-- if the user have a different membership other than BRONZE -->
            <a href="Profile.php" style="color:#ffff;">
              <button>Change Your Plan</button>
            </a>

          <?php } else { ?> <!-- this means that the header is empty and therefore the user did not log in -->
            <a href="signup.php" style="color:#ffff;">
              <button>Subscribe</button>
            </a>
          <?php } ?>
        </div>


        
      </div>

      <div class="plan">
        <h3><span id="GoldenColor">Golden</span> Memership Plan</h3><br>
        <hr> <br>
        <div class="PlanContent">

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>Access to our gym facilities 24/7</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>4 body checkup per month</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>16 inivitations per month</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>4 personal training sessions per month</p>
          </div>

          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>5 Group Sessions per month</p>
          </div>
        
          <div class="PlanBenefit">
            <img src="Imgs/Icons/check.png" alt="">
            <p>Spa Access</p>
          </div>

          <div class="price">
            <p>1650 EG / month</p>
          </div>
          <?php if (isset($_SESSION["Membership"]) && $_SESSION["Membership"] == "BRONZE") { ?> <!-- check if membership var is not empty and is Bronze -->
            <!-- displays the membership level of the logged in user by retrieving session variables -->
            <a style="color:#ffff;">
              <button id="subscribed">Subscribed <img src="ok.png" width="13" height="13">
              </button>
            </a>

          <?php } else if (isset($_SESSION["Membership"])) { ?> <!-- if the user have a different membership other than BRONZE -->
            <a href="Profile.php" style="color:#ffff;">
              <button>Change Your Plan</button>
            </a>

          <?php } else { ?> <!-- this means that the header is empty and therefore the user did not log in -->
            <a href="signup.php" style="color:#ffff;">
              <button>Subscribe</button>
            </a>
          <?php } ?>
        </div>






<!--         <h3>Golden Memership Plan</h3><br>
        <hr> <br>
        <p>Access to gym facilities 24/7 + Access to Spa room, 4 body checkups per month , 8 free personal training sessions, 16 invitations per month, 5 free group program sessions</p>
        <div class="price">
          <p>1350 EG / month</p>
        </div>
        <?php if (isset($_SESSION["Membership"]) && $_SESSION["Membership"] == "GOLD") { ?>
          <a style="color:#ffff;">
            <button id="subscribed">Subscribed<img src="ok.png" width="13" height="13"></button>
          </a>

        <?php } else if (isset($_SESSION["Membership"])) { ?>
          <a href="Profile.php" style="color:#ffff;">
            <button>Change Your Plan</button>
          </a>

        <?php } else { ?>

          <a href="signup.php" style="color:#ffff;">
            <button>Subscribe</button>
          </a>
        <?php } ?> -->
      </div>

    </div>
  </section>


  <section class="trainers">
    <h2>Our Trainers</h2>
    <hr id="title2">
    <div class="trainer-container">
      <div class="trainer">
        <img src="Imgs/Featured/Trainers/PTrainer.jpg" alt="Trainer 1">
        <h3>John Sika</h3>
        <p>Personal Trainer</p>
      </div>
      <div class="trainer">
        <img src="Imgs/Featured/Trainers/Nutritionist.jpg" alt="Trainer 2">
        <h3>Youssef Ahmed</h3>
        <p>Nutritionist</p>
      </div>
      <div class="trainer">
        <img src="Imgs/Featured/Trainers/Yoga.jpg" alt="Trainer 3">
        <h3>Mark Smith</h3>
        <p>Yoga Instructor</p>
      </div>
    </div>
  </section>

  <section class="about" id="toabout">
    <h2>About Us</h2>
    <p>


      Welcome to Epix Gym, where fitness meets excellence! At Epix, we are not just a gym; we are a community dedicated to providing a top-tier fitness experience that transcends the ordinary.

      Our commitment is clear: to create an environment that welcomes individuals from all walks of life, fostering a sense of inclusivity and empowerment. Epix Gym is designed to be your haven for achieving health and fitness goals, regardless of your current level of fitness or background.

      Step into Epix Gym, and you'll discover state-of-the-art facilities boasting the latest in fitness equipment and cutting-edge amenities. We've curated an environment where your fitness journey is not only supported but also elevated to new heights.

      Choose from a diverse range of fitness classes, including invigorating yoga, dynamic Pilates, high-energy spinning sessions, and more. Our certified trainers, driven by a passion for wellness, are here to guide you through personalized workout plans tailored to challenge and inspire.

      Epix Gym isn't just a place to work out; it's a vibrant community where connections are forged, encouragement is abundant, and success is celebrated together. Our members form a supportive network, ensuring that your fitness journey is not only effective but also enjoyable.

      Whether you're taking your first steps into fitness or looking to enhance your athletic prowess, Epix Gym is equipped to meet your diverse needs. Join us and experience the transformative benefits of embracing a healthy and active lifestyle.

      With the dedicated and knowledgeable staff at Epix Gym, you're not just a member; you're a valued individual on a personal fitness adventure. We understand that fitness is a journey of continuous improvement, and at Epix Gym, we're here to be your unwavering companions every step of the way. Welcome to Epix Gym, where your fitness story becomes epic!

    </p>
  </section>

  <section class="contact" id="tocontact">
    <h2>Contact Us</h2>
    <form>
      <input type="text" placeholder="Name">
      <input type="email" placeholder="Email">
      <textarea placeholder="Message"></textarea>
      <button type="submit">Send</button>
    </form>
  </section>

  <footer>
    <p>&copy; 2023 Epix Gym. All rights reserved.</p>
  </footer>
</body>

</html>