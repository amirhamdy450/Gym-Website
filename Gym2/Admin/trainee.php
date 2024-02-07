<?php
include ('img_handler.php');
// Connect to the database
$con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server");
$db = mysqli_select_db($con, "gym") or die("Error: Can't connect to database");

$id = $_GET['id'];

// Retrieve the current values of the record from the database
$sql = "SELECT * FROM trainee WHERE Tr_ID=$id";
$result = mysqli_query($con, $sql);
$trainee = mysqli_fetch_assoc($result);


if (isset($_POST['submit'])) {
  switch ($_POST['action']) {
    case 'add':
      $name = $_POST['name'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $birthDate = $_POST['birthdate'];
      $gender = $_POST['gender'];
      $dateJoined =  date('Y-m-d');
      $membershipLevel = $_POST['membershiplevel'];

      $sql = "INSERT INTO trainee (Tr_Name, Email, Password, Birth_Date, Gender, DateJoined, Membership_Level) 
              VALUES ('$name', '$email', '$password', '$birthDate', '$gender', '$dateJoined', '$membershipLevel')";
      $result = mysqli_query($con, $sql);
      $recent_id=mysqli_insert_id($con);    /* this functions helps us retrieve the id that was just created by the lates because it is auto incremented in the database and thhere is no way knowing it without it */
      img_handler($recent_id);       
      
      if ($result) {
        echo "Trainee added successfully!";
      } else {
        echo "Error adding trainee: " . mysqli_error($con);
      }

      break;

    case 'edit':
      // Retrieve the new values from the form
      $name = $_POST['name'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $birthDate = $_POST['birthdate'];
      $gender = $_POST['gender'];
      $dateJoined = $_POST['dateJoined'];
      $membershipLevel = $_POST['membershiplevel'];
      // Update the record in the database
      $sql = "UPDATE trainee SET Tr_Name='$name', Email='$email', Password='$password', 
              Birth_Date='$birthDate', Gender='$gender', DateJoined='$dateJoined', 
              Membership_Level='$membershipLevel' WHERE Tr_ID=$id";
      $result = mysqli_query($con, $sql);
     img_handler();     
     
      // Redirect back to the Admin page
      header("Location: Admin.php");
      exit();
  }
}

if ($_GET['action'] == "delete") {
  $sql = "DELETE FROM trainee WHERE Tr_ID=$id";
  $result = mysqli_query($con, $sql);

  if ($result) {
    // Redirect back to the Admin page
    header('Location: Admin.php');
  } else {
    echo '<script>alert("Error deleting record: ' . mysqli_error($con) . '");</script>';
  }

  exit();
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Trainee</title>
  <link rel="stylesheet" href="Panel.css">

</head>
<body>
  <h1>Admin Dashboard</h1>

  <?php if ($_GET['action'] == 'edit') { ?>
    <div class="container">
      <div class="window">
        <h2>Edit Trainee</h2>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?php echo $trainee['Tr_ID']; ?>">
          <input type="hidden" name="action" value="edit">
          <label for="name">Name:</label>
          <input type="text" name="name" value="<?php echo $trainee['Tr_Name']; ?>">
          <label for="email">Email:</label>
          <input type="email" name="email" value="<?php echo $trainee['Email']; ?>">
          <label for="password">Password:</label>
          <input type="password" name="password" value="<?php echo $trainee['Password']; ?>">
          <label for="birthdate">Birth Date:</label>
          <input type="date" name="birthdate" value="<?php echo $trainee['Birth_Date']; ?>">
          <label for="gender">Gender:</label>
          <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="M" <?php if ($trainee['Gender'] == 'M') echo 'selected'; ?>>Male</option>
            <option value="F" <?php if ($trainee['Gender'] == 'F') echo 'selected'; ?>>Female</option>
          </select>
          <label for="dateJoined">Date Joined:</label>
          <input type="date" name="dateJoined" value="<?php echo $trainee['DateJoined']; ?>">
          <label for="membershipLevel">Membership Level:</label>
          <select name="membershiplevel" required>
            <option value="">Select Membership</option>
            <option value="BRONZE"  <?php if ($trainee['Membership_Level'] == 'BRONZE') echo 'selected'; ?>>Bronze</option>
            <option value="SILVER" <?php if ($trainee['Membership_Level'] == 'SILVER') echo 'selected'; ?>>Silver</option>
            <option value="GOLD" <?php if ($trainee['Membership_Level'] == 'GOLD') echo 'selected'; ?>>Gold</option>
          </select>
          <label for="file_img">Profile Image:</label>
            <?php if (!isset($trainee['profile_img'])){ ?>
              <div class="unset"><h1>Not Set !</h1>
              <?php } else{ ?>

                <div class="unset">    <?php      echo '<img src="data:image/jpeg;base64,' . $trainee["profile_img"] . '" id="profile-picture-img" alt="Profile picture" >'; ?>


                <?php } ?>
           

          <input type="file"  id="choose" name="file_img"  accept=".jpg, .jpeg, .png, .gif" >   </div>

          <button type="submit" name="submit">Save Changes</button>
        </form>
      </div>
    </div>
  <?php } ?>

  <?php if ($_GET['action'] == 'add') { ?>
    <div class="container">
      <div class="window">
        <h2>Add Trainee</h2>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add">
          <label for="name">Name:</label>
          <input type="text" name="name" required>
          <label for="email">Email:</label>
          <input type="email" name="email" required>
          <label for="password">Password:</label>
          <input type="password" name="password" required>
          <label for="birthdate">Birth Date:</label>
          <input type="date" name="birthdate" required>
          <label for="gender">Gender:</label>
          <select name="gender" required>
            <option value="M">Male</option>
            <option value="F">Female</option>
          </select>

          <label for="membershipLevel">Membership Level:</label>
          <select name="membershiplevel" required>
          <option value="">Select Membership</option>
            <option value="BRONZE"  >Bronze</option>
            <option value="SILVER" >Silver</option>
            <option value="GOLD" >Gold</option>
          </select>
          <label for="file_img">Profile Image:</label>

          <input type="file" id="choose" name="file_img"  accept=".jpg, .jpeg, .png, .gif">   

          <button type="submit" name="submit">Add Trainee</button>
        </form>
      </div>
    </div>
  <?php } ?>

</body>
</html>
