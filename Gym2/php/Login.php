<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="google-signin-client_id" content="1007872885686-0vlndmia2pmoub2mn9ov3u0cep98uffs.apps.googleusercontent.com">  <!-- include the client id we created at google cloud to use google sign-in -->
  <title>Login</title>

  <link rel="stylesheet" href="/Gym2/CSS/Login.css">
  <link rel="stylesheet" href="/Gym2/CSS/Error.css">
  <script src="Gbutton.js"></script>     
  <script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>

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



  <section class="login">

  <?php if (isset($_GET['error'])) { ?>  <!-- same as the signup.php  logic   -->

<p class="error"><?php echo $_GET['error']; ?></p>

<?php } ?>

    <h2>Login</h2>
    <form method="POST" action="verify.php">    <!-- saperate the verification to another page unlike the signup.php -->
      <div class="form-group">
        <label for="email">Email</label>
        <input type="text" id="Email" name="Email" required>
      </div>
      <div class="form-group">
        <label for="Password">Password</label>
        <input type="Password" id="Password" name="Password" required>
        <input type="checkbox" id="save" name="Remember_me" value="saveme"> 
        <label for="save">Remember Me</label>

      </div>
      <div class="form-group">
        <button type="submit">Login</button>
        <div  class="g-login" id="gbutton"  data-onsuccess="onSignIn"></div>


        <div class="signup">
        <span>Don't have an account? <a href="type.php">Sign up</a></span>
        </div>

      </div>
    </form>
  </section>

  <footer>
    <p>&copy; 2023 Epix Gym. All rights reserved.</p>
  </footer>
</body>
</html>