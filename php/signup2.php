<?php
  
if (isset($_GET['filled']) && $_GET['filled'] != 'true'){
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_SESSION["first_name"] . " " . $_SESSION["last_name"];
  $bday = $_SESSION["birthday"];
  $gender = $_SESSION["gender"];
  $email = $_SESSION["email"];
  $membership = $_POST["membership_level"];
  $password = $_SESSION["password"];
  $cpassword = $_SESSION["password_confirmation"];

  $current_time = date('Y-m-d');


  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.php?error=Incorrect Email Format !");
    exit();
  }

  $con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server:( ");
  $db = mysqli_select_db($con, "gym") or die("Error Can't connect to Database !");
  $Insert_User="INSERT INTO trainee(Tr_Name,Email,Password,Birth_Date,Gender,DateJoined,Membership_Level) VALUE('$name','$email','$password','$bday','$gender','$current_time','$membership')";

  $sql_check = mysqli_query($con, "SELECT * FROM trainee WHERE Email='$email'");
  if (mysqli_num_rows($sql_check) > 0) {
    header("Location: signup.php?error=Email Already Exists !");
    exit();
  }

  $sql_check = mysqli_query($con, "SELECT * FROM trainee WHERE Email='$email'");
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="/Gym2/CSS/signup.css">
  <link rel="stylesheet" href="/Gym2/CSS/Error.css">
 


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

  <?php if (isset($_GET['error'])) { ?>

<p class="error">
 
<strong>
<?php
 echo $_GET['error']."<br>"; ?> </strong>
 


</p>

<?php } ?>

<h2>Select Your Plan </h2>
    <form method="POST" action="">
      
  
      <div class="form-group" id="type">
        <label for="membership_level">Membership Level</label>
        <select  name="membership_level" id="membership_level" required>
          <option value="">Select Membership Level</option>
          <option value="bronze">Bronze</option>
          <option value="silver">Silver</option>
          <option value="gold">Gold</option>
        </select>
      </div>

   <div class="form-group" id="price-container">
    <label for="price">Membership Price</label>
    <div id="price">
      120 $
    </div>

    <div id="details-box">
    <h3 id="details-header">Membership Details</h3>
    <p id="details"></p>
    </div>

  </div>

      <script>
  const membershipLevel = document.getElementById('membership_level');
  const membershipDetails = document.getElementById('membership-details');
  const price = document.getElementById("price");
  // Add event listener to the dropdown
  membershipLevel.addEventListener('change', function() {
    const selectedOption = this.value;
    if(selectedOption === 'bronze') {
      price.innerHTML=("<p>A7A</p>");
      membershipDetails.style.display = 'none'; // Hide the details input field
    } else if(selectedOption === 'silver') {
      // Handle silver membership
      membershipDetails.style.display = 'block'; // Show the details input field
    } else if(selectedOption === 'gold') {
      // Handle gold membership
      membershipDetails.style.display = 'block'; // Show the details input field
    } else {
      // Handle error case
      console.error('Invalid membership level');
    }
  });
</script>


  



      <div class="form-group">
        <button type="submit">Sign Up</button>
 
  
      </div>
    </form>
  </section>

   <footer>
    <p>&copy; 2023 Gym. All rights reserved.</p>
  </footer> 
</body>
</html>