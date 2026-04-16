<?php
session_start();
require_once "dbFuncs.php";

$pdo = connectDB();

// Use a LEFT JOIN to include events even if they have 0 RSVPs
$sql = "SELECT e.*, COUNT(r.event_id) AS rsvp_count 
        FROM events e 
        LEFT JOIN events_rsvp r ON e.event_id = r.event_id 
        GROUP BY e.event_id 
        ORDER BY e.event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();

//connect to db and gets events data 
$events = $stmt->fetchAll();

//return events as JSON instead of PHP array 
header('Content-Type: application/json');
echo json_encode($events);

?>