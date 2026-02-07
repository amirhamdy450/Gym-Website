<?php 
include "../Server/DB.php" ;
include "../Server/Operations.php" ;

?>



<!DOCTYPE html>
<html>

<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="Admin.css">
  <link rel="stylesheet" href="../CSS/global.css">

  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet" />



</head>

<body class="Admin">
  <?php include "Nav.php" ?>

  <div class="AdminWindowsCont">


    <div class="window">
      <h2>Instructors</h2>
      <a class="AdminAdd" ref="instructor">Add Instructor</a>
      <table>
        <thead>
            <?php 
            $instructorHeaders = ['Name', 'City', 'Nationality','Gender', 'Email', 'Age', 'Salary', 'Employment Date','On Vacation'];
            foreach ($instructorHeaders as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
              }


            echo '<th> Actions </th>' ?>
        </thead>
        <tbody>
          <?php 
                $sql = 'SELECT * FROM instructor';
                $stmt = $pdo->prepare($sql);
                if(!$stmt->execute()){
                    die('Error: ' . $stmt->errorInfo()[2]);

                }

                $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($instructors as $instructor) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($instructor['Name']) . '</td>';
                    echo '<td>' . htmlspecialchars($instructor['City']) . '</td>';
                    echo '<td>' . htmlspecialchars($instructor['Nationality']) . '</td>';
                    echo '<td>' . htmlspecialchars($instructor['Gender']) . '</td>';
                    echo '<td>' . htmlspecialchars($instructor['Email']) . '</td>';
                    echo '<td>' . htmlspecialchars($instructor['BirthDate']) . '</td>';
                    echo '<td>' . htmlspecialchars($instructor['Salary']) . '</td>';
                    echo '<td>' . htmlspecialchars($instructor['Emp_Date']) . '</td>';
                    if($instructor['OnVacation']){
                      echo '<td>Yes</td>';
                    }else{
                      echo '<td>No</td>';
                    }
                    echo '<td class="Actions">
                    <a class="AdminEdit" ref="instructor" rid="'.$instructor['id'].'">Edit</a>
                    <a class="AdminDelete" ref="instructor" rid="'.$instructor['id'].'">Delete</a>
                    </td>';
                }

          ?>
        </tbody>
      </table>
    </div>



    <div class="window">
      <h2>Programs</h2>
      <a class="AdminAdd" ref="Program">Add Program</a>
      <table>
        <thead>
            <?php 
            $instructorHeaders = ['Name', 'Description', 'Price','Start Date', 'End Date'];
            foreach ($instructorHeaders as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
              }


            echo '<th> Actions </th>' ?>
        </thead>
        <tbody>
          <?php 
              $sql = 'SELECT 
              p.id,
              p.Name ,
              p.Description,
              p.price ,
              s.StartDate,
              s.EndDate 
              FROM Programs p
              INNER JOIN program_schedules s ON p.id = s.ProgramID
              ORDER BY s.StartDate DESC';

                $stmt = $pdo->prepare($sql);
                if(!$stmt->execute()){
                    die('Error: ' . $stmt->errorInfo()[2]);

                }

                $Programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($Programs as $Program) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($Program['Name']) . '</td>';
                    echo '<td>' . htmlspecialchars($Program['Description']) . '</td>';
                    echo '<td>' . htmlspecialchars($Program['price']) . '</td>';
                    echo '<td>' . htmlspecialchars($Program['StartDate']) . '</td>';
                    echo '<td>' . htmlspecialchars($Program['EndDate']) . '</td>';
                    echo '<td class="Actions">
                    <a class="AdminEdit" ref="Program" rid="'.$Program['id'].'">Edit</a>
                    <a class="AdminDelete" ref="Program" rid="'.$Program['id'].'">Delete</a>
                    </td>';
                }

          ?>
        </tbody>
      </table>
    </div>

    <div id="calendar"></div>

    <?php

/*     $sql = 'SELECT * FROM supplement';
    $result = mysqli_query($con, $sql);
    // Check if the query was successful
    if (!$result) {
      die('Error: ' . mysqli_error($con));
    } */
    $suplements = [];
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

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

  <script  src="Admin.js" ></script>
</body>

</html>