<?php session_start(); 

include "Cart_Session.php";


?>
<!DOCTYPE html>
<html>
<body>

<?php /*  this file is for testing and debugging values and is not part of the project  */
$img=$_SESSION["id"];
echo "<h1 style='text-align:center;'> welcome to debugger ! here are your printed values </h1> <hr>";
echo var_dump($_SESSION);
echo"<hr>".var_dump($responseArray);
echo $removeclass;


?>

</body>
</html>