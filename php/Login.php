<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="google-signin-client_id" content="1007872885686-0vlndmia2pmoub2mn9ov3u0cep98uffs.apps.googleusercontent.com">  
  <title>Login</title>


  <link rel="stylesheet" href="/projects/GYM2/CSS/global.css">

  <link rel="stylesheet" href="/projects/GYM2/CSS/Login.css">
  <link rel="stylesheet" href="/projects/GYM2/CSS/Error.css">
  <script src="Gbutton.js"></script>     
  <script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>

</head>
<body class="Login">
  <nav>
    <div class="logo">
      <a href="../index.php">Epix Gym</a>
    </div>
    <ul class="nav-links">
      <li><a href="../index.php">Home</a></li>
      <li><a href="../index.php #toplans">Plans</a></li>
      <li><a href="../index.php #toabout">About Us</a></li>
      <li><a href="../index.php #tocontact">Contact Us</a></li>
      <li><a href="">Login</a></li>
     
    </ul>
  </nav>



  <section class="LoginCont">

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
        <button type="submit" class="BrandBtn">Login</button>
        <button type="button" class="BrandBtn Icon Google">
          <img src="../Imgs/Icons/Google.svg" alt="">
          Sign in with Google
        </button>

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