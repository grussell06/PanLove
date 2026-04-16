<?php
session_start();
require_once "dbFuncs.php";

$pdo = connectDB();

if (!isset($_SESSION["user_id"])) {
    echo "User not logged in";
    exit();
}

$user_id = $_SESSION["user_id"];
$event_id = $_POST["event_id"];


// Insert RSVP into database
try {
    $sql = "INSERT INTO events_rsvp (user_id, event_id, status)
            VALUES (?, ?, 'going') ON DUPLICATE KEY UPDATE status = 'going'";
            //values has ?, ? since it's enum for status 

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $event_id]);

    echo "success";

} catch (Exception $e) {
    echo "error";
}
?>