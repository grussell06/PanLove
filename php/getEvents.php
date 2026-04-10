<?php
session_start();
require_once "dbFuncs.php";

$pdo = connectDB();

$sql = "SELECT * FROM events ORDER BY event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();

//connect to db and gets events data 
$events = $stmt->fetchAll();

//return events as JSON instead of PHP array 
header('Content-Type: application/json');
echo json_encode($events);

?>