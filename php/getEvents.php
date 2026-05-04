<?php
session_start();
require_once "dbFuncs.php";

$pdo = connectDB();

$user_id = $_SESSION["user_id"] ?? null;

// Use a LEFT JOIN to include events even if they have 0 RSVPs
$sql = "SELECT e.*, 
               COUNT(CASE WHEN r.status = 'going' THEN 1 END) AS rsvp_count,
               CASE WHEN ? IS NOT NULL AND EXISTS(SELECT 1 FROM events_rsvp r2 WHERE r2.event_id = e.event_id AND r2.user_id = ? AND r2.status = 'going') THEN 1 ELSE 0 END AS user_rsvped
        FROM events e 
        LEFT JOIN events_rsvp r ON e.event_id = r.event_id 
        GROUP BY e.event_id 
        ORDER BY e.event_date ASC, e.event_time ASC"; //order by date and time, soonest first
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $user_id]);

//connect to db and gets events data 
$events = $stmt->fetchAll();

//filter out past events, only show events that are today or in the future
$now = new DateTime();

$upcomingEvents = [];

foreach ($events as $event) {
    $eventDate = new DateTime($event['event_date']);

    if ($eventDate >= $now) {
        $upcomingEvents[] = $event;
    }
}

//return events as JSON instead of PHP array 
header('Content-Type: application/json');
echo json_encode($upcomingEvents);

?>