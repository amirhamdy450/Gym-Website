<?php
include ('img_handler.php');

// Connect to the database
$con = mysqli_connect("localhost", "root", "") or die("Error: can't connect to server");
$db = mysqli_select_db($con, "gym") or die("Error: Can't connect to database");

$id = $_GET['id'];

// Retrieve the current values of the record from the database
$sql = "SELECT * FROM supplement WHERE S_ID=$id";
$result = mysqli_query($con, $sql);
$supplement = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
  switch ($_POST['action']) {
    case 'add':
      $name = $_POST['name'];
      $type = $_POST['type'];
      $cost = $_POST['cost'];
      $brand =$_POST['brand'];

      $sql = "INSERT INTO supplement (S_Name, S_Type, S_Price, S_Brand) VALUES ('$name', '$type', '$cost','$brand')";
      $result = mysqli_query($con, $sql);
      $recent_id=mysqli_insert_id($con);    /* this functions helps us retrieve the id that was just created by the lates because it is auto incremented in the database and thhere is no way knowing it without it */
      img_handler($recent_id);

      if ($result) {
        echo "Supplement added successfully!";
      } else {
        echo "Error adding supplement: " . mysqli_error($con);
      }

      break;

    case 'edit':
      // Retrieve the new values from the form
      $name = $_POST['name'];
      $type = $_POST['type'];
      $cost = $_POST['cost'];
      $brand =$_POST['brand'];

      // Update the record in the database
      $sql = "UPDATE supplement SET S_Name='$name', S_Type='$type', S_Price='$cost',S_Brand='$brand' WHERE S_ID=$id";
      $result = mysqli_query($con, $sql);
      img_handler();
      // Redirect back to the supplements page
      header("Location: Admin.php");
      exit();
  }
}

if ($_GET['action'] == "delete") {
  $sql = "DELETE FROM supplement WHERE S_ID=$id";
  $result = mysqli_query($con, $sql);

  if ($result) {
    // Redirect back to the supplements page
    header("Location: Admin.php");
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
  <title>Supplement</title>
  <link rel="stylesheet" href="Panel.css">

</head>
<body>
  <h1>Admin Dashboard</h1>

  <?php if ($_GET['action'] == 'edit') { ?>
    <div class="container">
      <div class="window">
        <h2>Edit Supplement</h2>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?php echo $supplement['S_ID']; ?>">
          <input type="hidden" name="action" value="edit">
          <label for="name">Name:</label>
          <input type="text" name="name" value="<?php echo $supplement['S_Name']; ?>">
          <label for="brand">Brand Name:</label>
          <input type="text" name="brand" value="<?php echo $supplement['S_Brand']; ?>">
          <label for="type">Type:</label>
          <input type="text" name="type" value="<?php echo $supplement['S_Type']; ?>">
          <label for="cost">Cost:</label>
          <input type="number" name="cost" value="<?php echo $supplement['S_Price']; ?>">

          <label for="vars">Variants:</label>

      <table name="vars"> 
        <tbody>
          <tr>
          <th>Flavor</th>
          <th>Extra Price</th>
          <th>Total Product Price</th>
         </tr>
         <tr>
          <td>Alfreds Futterkiste</td>
          <td>Maria Anders</td>
          <td>Germany</td>
        </tr>
        <tr>
         <td>Centro comercial Moctezuma</td>
         <td>Francisco Chang</td>
          <td>Mexico</td>
        </tr>
       </tbody>
    </table>


    <table name="vars"> 
        <tbody>
          <tr>
          <th>Size</th>
          <th>Extra Price</th>
          <th>Total Product Price</th>
         </tr>
         <tr>
          <td>Alfreds Futterkiste</td>
          <td>Maria Anders</td>
          <td>Germany</td>
        </tr>
        <tr>
         <td>Centro comercial Moctezuma</td>
         <td>Francisco Chang</td>
          <td>Mexico</td>
        </tr>
       </tbody>
    </table>





          <label for="profile-picture-img">Product Image:</label>
          <?php if (!isset($supplement['S_img'])){ ?>
            <div class="unset"><h1>Not Set !</h1>
            <?php } else{ ?>

             <div class="unset">    <?php      echo '<img src="data:image/jpeg;base64,' . $supplement["S_img"] . '" id="profile-picture-img" alt="Profile picture" >'; ?>
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
        <h2>Add Supplement</h2>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add">
          <label for="name">Name:</label>
          <input type="text" name="name" required>
          <label for="brand">Brand Name:</label>
          <input type="text" name="brand" required>
          <label for="type">Type:</label>
          <input type="text" name="type" required>
          <label for="flavor">Flavor:</label>
          <input type="text" name="flavor" required>
          <label for="cost">Cost:</label>
          <input type="text" name="cost" required>
          <label for="file_img">Product Image:</label>
 
           

          <input type="file"  id="choose" name="file_img"  accept=".jpg, .jpeg, .png, .gif" >   </div>

        
        
          <button type="submit" name="submit">Add Supplement</button>
        </form>
      </div>
    </div>
  <?php } ?>

</body>
</html>
