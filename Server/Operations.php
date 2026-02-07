<?php 
function FormatTimestamp($timestamp,$type="approximate") {
    if($type == "exact"){
        return date("d-m-Y", $timestamp);
    }
    $current_time = time();
    $time_difference = $current_time - $timestamp;

    // Calculate hours, days, weeks, months, and years
    $hours_ago = floor($time_difference / 3600);
    $days_ago = floor($time_difference / 86400);
    $weeks_ago = floor($days_ago / 7);
    $months_ago = floor($days_ago / 30);
    $years_ago = floor($days_ago / 365);

    if ($hours_ago < 24) { // Less than 24 hours
        if ($hours_ago < 1) { // Less than one hour
            return "Just Now";
        } else { // Display the number of hours
            return $hours_ago . " hour" . ($hours_ago > 1 ? "s" : "") . " ago";
        }
    } elseif ($days_ago < 7) { // Less than one week
        if ($days_ago < 2) {
            return "Yesterday";
        } else {
            return $days_ago . " day" . ($days_ago > 1 ? "s" : "") . " ago";
        }
    } elseif ($days_ago < 30) { // Less than one month
        return $weeks_ago . " week" . ($weeks_ago > 1 ? "s" : "") . " ago";
    } elseif ($days_ago < 365) { // Less than one year
        return $months_ago . " month" . ($months_ago > 1 ? "s" : "") . " ago";
    } else { // In years
        return $years_ago . " year" . ($years_ago > 1 ? "s" : "") . " ago";
    }
}

function CalculateAge($Timestamp){
//get current time
$current_time = strtotime('now');

//calculate timestamp difference
$time_difference = $current_time - $Timestamp;

//calculate age
$age = floor($time_difference / 31556926);
return $age;



}


//echo CalculateAge(823465603); // Output: 52
//echo FormatTimestamp(1620000000,'exact'); // Output: "1 week ago"

?>