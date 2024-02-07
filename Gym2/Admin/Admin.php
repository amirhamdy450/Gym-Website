

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="Admin.css">

</head>
<body>
  <h1>Admin Dashboard</h1>
    <div class="window">
      <h2>Instructors</h2>
      <a href="instructors.php?action=add&id=0">Add Instructor</a>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>City</th>
            <th>BirthDate</th>
            <th>Gender</th>
            <th>Salary</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <?php 
          $con = mysqli_connect("localhost","root","") or die ("Error: can't connect to server:( ");
          $db =  mysqli_select_db($con,"gym") or die ("Error Can't connect to Database !");
          $sql = 'SELECT * FROM instructor';
          $result = mysqli_query($con, $sql);
          // Check if the query was successful
          if (!$result) {
         die('Error: ' . mysqli_error($con));
         }

         // Store the data in the $instructor variable
         $instructors = mysqli_fetch_all($result, MYSQLI_ASSOC);
          ?>

          <?php foreach ($instructors as $ins): ?>
            <td><?php echo $ins['Ins_Name']; ?></td>
            <td><?php echo $ins['Email']; ?></td>
            <td><?php echo $ins['City']; ?></td>
            <td><?php echo $ins['BirthDate']; ?></td>
            <td><?php echo $ins['Gender']; ?></td>
            <td><?php echo $ins['Salary']; ?></td>

            <td>
              <a href="instructors.php?action=edit&id=<?php echo $ins['Ins_ID']; ?>">Edit</a>
              <a href="instructors.php?action=delete&id=<?php echo $ins['Ins_ID']; ?>"  onclick="return confirm('Are you sure you want to delete this program?') ? window.location.href = this.href : false;">Remove</a>
            </td>
          </tr>
          <?php endforeach; ?>



        </tbody>
      </table>
    </div>
    

    <div class="window">
  <h2>Trainees</h2>
  <a href="trainee.php?action=add&id=0">Add Trainee</a>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>BirthDate</th>
        <th>Gender</th>
        <th>Date Joined</th>
        <th>Membership Level</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php 

      $sql = "SELECT * FROM trainee";
      $result = mysqli_query($con, $sql);

      // Check if the query was successful
      if (!$result) {
        die('Error: ' . mysqli_error($con));
      }

      // Store the data in the $trainees variable
      $trainees = mysqli_fetch_all($result, MYSQLI_ASSOC);
      ?>

      <?php foreach ($trainees as $trainee): ?>
        <tr>
          <td><?php echo $trainee['Tr_Name']; ?></td>
          <td><?php echo $trainee['Email']; ?></td>
          <td><?php echo $trainee['Birth_Date']; ?></td>
          <td><?php echo $trainee['Gender']; ?></td>
          <td><?php echo $trainee['DateJoined']; ?></td>
          <td><?php echo $trainee['Membership_Level']; ?></td>

          <td>
            <a href="trainee.php?action=edit&id=<?php echo $trainee['Tr_ID']; ?>">Edit</a>
            <a href="trainee.php?action=delete&id=<?php echo $trainee['Tr_ID']; ?>" onclick="return confirm('Are you sure you want to delete this trainee?') ? window.location.href = this.href : false;">Remove</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>


    <?php 

              $sql = 'SELECT * FROM program';
              $result = mysqli_query($con, $sql);
              // Check if the query was successful
              if (!$result) {
             die('Error: ' . mysqli_error($con));
             }
             $programs=mysqli_fetch_all($result,MYSQLI_ASSOC);
    ?>


    <div class="window">
      <h2>Programs</h2>
      <a href="program.php?action=add&id=0">Add Program</a>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Duration</th>

            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <?php foreach ($programs as $pr): ?>
            <td><?php echo $pr['Program_Type']; ?></td>
            <td><?php echo $pr['Price']; ?> EG</td>
            <td><?php echo $pr['No_Of_Days']; ?></td>

            <td>
              <a href="program.php?action=edit&id=<?php echo $pr['Program_Number']; ?>">Edit</a>
              <a href="program.php?action=delete&id=<?php echo $pr['Program_Number']; ?>"  onclick="return confirm('Are you sure you want to delete this program?') ? window.location.href = this.href : false;">Remove</a>
            </td>

          </tr>
          <?php endforeach; ?>

        </tbody>
      </table>
    </div>

    <?php 

$sql = 'SELECT * FROM supplement';
$result = mysqli_query($con, $sql);
// Check if the query was successful
if (!$result) {
die('Error: ' . mysqli_error($con));
}
$suplements=mysqli_fetch_all($result,MYSQLI_ASSOC);
?>





<div class="window">
<h2>Suplements</h2>
<a href="sup.php?action=add&id=0">Add A Suplement</a>
<table>
<thead>
<tr>
<th>Name</th>
<th>Price</th>
<th>Type</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<tr>
<?php foreach ($suplements as $sp): ?>
<td><?php echo $sp['S_Name']; ?></td>
<td><?php echo $sp['S_Price']; ?> EG</td>
<td><?php echo $sp['S_Type']; ?></td>
<td>
<a href="sup.php?action=edit&id=<?php echo $sp['S_ID']; ?>">Edit</a>
<a href="sup.php?action=delete&id=<?php echo $sp['S_ID']; ?>"  onclick="return confirm('Are you sure you want to delete this program?') ? window.location.href = this.href : false;">Remove</a>
</td>

</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>
</div>
</body>
</html>

