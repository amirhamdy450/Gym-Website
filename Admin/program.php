<?php

// Connect to the database
$con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server:( ");
$db = mysqli_select_db($con, "gym") or die("Error Can't connect to Database :(");
$id = $_GET['id'];

// Retrieve the current values of the record from the database
$sql = "SELECT * FROM program WHERE Program_Number=$id";
$result = mysqli_query($con, $sql);
$pr = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
  switch ($_POST['action']) {
    
    case 'add':
        $name = $_POST['name'];
        $price = $_POST['price'];
        $duration = $_POST['duration'];
  
      $sql = "INSERT INTO program(Program_Type,price,No_Of_Days)  VALUES ('$name', '$price', '$duration');";
      $result = mysqli_query($con, $sql);

      if ($result) {
        echo "Program added successfully!";
      } else {
        echo "Error adding instructor: " . mysqli_error($con);
      }
      
      break;

    case 'edit':
      // Retrieve the new values from the form
      $name = $_POST['name'];
      $price = $_POST['price'];
      $duration = $_POST['duration'];


      // Update the record in the database
      $sql = "UPDATE program SET Program_Type='$name', Price='$price' , No_Of_Days='$duration' WHERE Program_Number=$id";
      $result = mysqli_query($con, $sql);
      if ($result){
        // Redirect back to the instructors page
        header('Location: Admin.php');}
        else{
          echo '<script>alert("Error updating record.' . mysqli_error($con) . '");</script>';

        }
        exit();


    }


       
      
  }
   
  if ($_GET['action']=="delete"){


  // Display an alert message to confirm the deletion
      
 $sql = "DELETE FROM program WHERE Program_Number=$id";
 $result = mysqli_query($con, $sql);
                
if ($result){
  // Redirect back to the instructors page
   header('Location: Admin.php');}
   else{
 echo '<script>alert("Error deleting record.' . mysqli_error($con) . '");</script>';
        
     }
                exit();
            }
mysqli_close($con);


?>
<!DOCTYPE html>
<html>
<head>
  <title>Instructor</title>
  <link rel="stylesheet" href="Panel.css">

</head>
<body>
  <h1>Admin Dashboard</h1>
  <?php if ($_GET['action'] == 'edit') { ?>

    <div class="container">
      <div class="window">
        <h2>Edit Program</h2>
        <form method="post">
          <input type="hidden" name="id" value="<?php echo $pr['Program_Number']; ?>">
          <input type="hidden" name="action" value="edit">
          <label for="name">Name:</label>
          <input type="text" name="name" value="<?php echo $pr['Program_Type']; ?>">
          <label for="price">Price:</label>
          <input type="number" name="price" value="<?php echo $pr['Price']; ?>">
          <label for="duration">Duration:</label>
          <input type="number" name="duration" value="<?php echo $pr['No_Of_Days']; ?>">
          <button type="submit" name="submit">Save Changes</button>
        </form>
      </div>
    </div>
    <?php } ?>

    <?php if ($_GET['action'] == 'add') { ?>
      <div class="container">
    <div class="window">
      <h2>Add Program</h2>
      <form method="post">
        <input type="hidden" name="action" value="add">
        <label for="name">Name:</label>
        <input type="text" name="name" required>
        <label for="price">Price:</label>
        <input type="number" name="price" required>
        <label for="duration">Duration:</label>
        <input type="number" name="durations" required>
        <button type="submit" name="submit">Add Program</button>
      </form>
    </div>
  </div>
  <?php } ?>


</body>
</html>
