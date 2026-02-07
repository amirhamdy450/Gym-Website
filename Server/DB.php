<?php
date_default_timezone_set('Africa/Cairo');

$DBservername = "localhost";
$DBusername = "root";
$DBpassword = "";
$DBName = "gym";

try {
  $pdo = new PDO("mysql:host=$DBservername;dbname=$DBName", $DBusername, $DBpassword, 
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  
  echo "Connection failed: " . $e->getMessage();
}



function RunQuery($type,$options = []){
  global $pdo;
  $type = isset($options['type']) ? strtoupper($options['type']) : 'SELECT'; //Type of Query ,Default is SELECT
  $columns = isset($options['columns']) ? implode(", ", $options['columns']) : '*'; //Columns to be selected, Default is *
  $where = isset($options['where']) ? 'WHERE ' . implode(' AND ', $options['where']) : ''; //Checks if there is a WHERE clause
  $join = isset($options['join']) ? implode(' ', $options['join']) : '';
  $values = isset($options['values']) ? $options['values'] : [];
  $set = isset($options['set']) ? implode(', ', $options['set']) : '';



}



?>