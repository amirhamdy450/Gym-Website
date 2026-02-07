<?php 
include "../Server/DB.php" ;

function DisplayTable($DB_Table, $Columns, $Type, $EditBtn='none', $DeleteBtn='none', $Join=''){
    global $pdo; // Assuming $pdo is your PDO database connection

    // Create the SQL query with the specified columns
    $columnsString = implode(", ", $Columns);
    $sql = "SELECT id,$columnsString FROM $DB_Table $Join";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute();

    if (!$success) {
        die('Error: ' . $stmt->errorInfo()[2]);
    }



    if (strtolower($Type) == 'head') {
        echo '<tr>';
        foreach ($Columns as $Column) {
          $ColumnName = strpos($Column, ' AS ') !== false ? explode(' AS ', $Column)[1] : $Column;
          echo '<th>' . htmlspecialchars($ColumnName) . '</th>';
        }
        echo '<th>Actions</th>';
        echo '</tr>';
    } elseif (strtolower($Type) == 'body') {
        $Data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($Data as $Row) {
            echo '<tr>';
            foreach ($Columns as $Column) {
              $ColumnName = strpos($Column, ' AS ') !== false ? explode(' AS ', $Column)[1] : $Column;
              echo '<td>' . htmlspecialchars($Row[$ColumnName]) . '</td>';
          }
            if ($EditBtn != 'none' || $DeleteBtn != 'none') {
                echo '<td class="Actions">';
                if ($EditBtn != 'none') {
                    echo '<a class="'.$EditBtn.'" rid="'.$Row['id'].'">Edit</a>';
                }
                if ($DeleteBtn != 'none') {
                    echo '<a class="'.$DeleteBtn.'" rid="'.$Row['id'].'">Remove</a>';
                }
                echo '</td>';
            }
            echo '</tr>';
        }
    }
}
?>



<!DOCTYPE html>
<html>

<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="Admin.css">
  <link rel="stylesheet" href="../CSS/global.css">


</head>

<body class="Admin">
  <nav>
    <h1>Admin Dashboard</h1>
    <div class="BurgerBtn">
      <img src="../Imgs/Icons/BurgertBtn.svg" alt="">
    </div>
  </nav>

  <div class="AdminWindowsCont">


    <div class="window">
      <h2>Instructors</h2>
      <a href="instructors.php?action=add&id=0" class="AddBtn">Add Instructor</a>
      <table>
        <thead>
             <?php 
              $Columns=["Name", "City", "Nationality", "Email","Salary","Emp_Date AS EmploymentDate"];

             DisplayTable("instructor",$Columns,"head"); ?>
        </thead>
        <tbody>
          <?php DisplayTable("instructor",$Columns,"body","EditInstructorBtn","DeleteInstructorBtn"); ?>
        </tbody>
      </table>
    </div>


    <div class="window">
      <h2>Trainees</h2>
      <a class="AddBtn" href="trainee.php?action=add&id=0">Add Trainee</a>
      <table>
        <thead>
          <?php DisplayTable("trainee",["Tr_Name", "Email", "Birth_Date", "Gender", "DateJoined", "Membership_Level"],"head"); ?>
        </thead>
        <tbody>
          <?php DisplayTable("trainee",["Tr_Name", "Email", "Birth_Date", "Gender", "DateJoined", "Membership_Level"],"body","EditTraineeBtn","DeleteTraineeBtn"); ?>
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
    $programs = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
                <a href="program.php?action=delete&id=<?php echo $pr['Program_Number']; ?>" onclick="return confirm('Are you sure you want to delete this program?') ? window.location.href = this.href : false;">Remove</a>
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
    $suplements = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
                <a href="sup.php?action=delete&id=<?php echo $sp['S_ID']; ?>" onclick="return confirm('Are you sure you want to delete this program?') ? window.location.href = this.href : false;">Remove</a>
              </td>

          </tr>
        <?php endforeach; ?>

        </tbody>
      </table>
    </div>
  </div>
</body>

</html>