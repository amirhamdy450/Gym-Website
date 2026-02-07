<?php
include "../DB.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

//validate the permissions of the user
//Soon to be implemented



  function getDayIndex($day) {
    $days = [
        'Sunday' => 0,
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6
    ];
    return $days[$day];
  }


  // API Request - process data and return JSON

    if(isset($_GET['ProgramID'])){
      $ProgramID = intval($_GET['ProgramID']);
      $sql = "SELECT 
              p.Name ,
              ps.StartDate,
              ps.EndDate,
              ps.RoomNo,
              psd.Day,
              psd.StartTime,
              psd.EndTime
          FROM programs p
          JOIN program_schedules ps ON p.id = ps.ProgramID
          JOIN program_schedule_days psd ON ps.id = psd.ProgramScheduleID
          WHERE p.id = ?";
      $stmt = $pdo->prepare($sql);
      if($stmt->execute([$ProgramID])){
        $rows = $stmt->fetchAll();

        foreach ($rows as $row) {
          $startDate = date('Y-m-d', $row['StartDate']);
          $endDate = date('Y-m-d', $row['EndDate']);

          $events[] = [
              'title' => $row['Name'] . " in Room " . $row['RoomNo'],
              'startRecur' => $startDate,
              'endRecur' => $endDate,
              'daysOfWeek' => [getDayIndex($row['Day'])],
              'startTime' => $row['StartTime'],
              'endTime' => $row['EndTime']
          ];
      }

      header('Content-Type: application/json');
      echo json_encode($events);


      }else{
        echo json_encode(
          [
            'success'=>false,
            'message'=>'Error Fetching Data',
          ]
        );
      }



    }else{
      echo json_encode(
      [
        'success'=>false,
        'message'=>'Invalid Request',
      
        ]
          
        
        );
    }



}
?>