<?php
session_start();
require_once "dbFuncs.php";

// Set header so the browser knows to expect JSON
header('Content-Type: application/json');

$pdo = connectDB();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "not_logged_in"]);
    exit();
}

$user_id = $_SESSION["user_id"];
$event_id = $_POST["event_id"] ?? null;

if (!$event_id) {
    echo json_encode(["status" => "error", "message" => "No event ID provided"]);
    exit();
}

// Insert RSVP into db
try {
     // check if RSVP already exists
    $check = $pdo->prepare("SELECT * FROM events_rsvp WHERE user_id = ? AND event_id = ?");
    $check->execute([$user_id, $event_id]);
    $existing = $check->fetch();

    $status = "";

    if ($existing) {

        // toggle off and delete it if it alr exists 
        $delete = $pdo->prepare("DELETE FROM events_rsvp WHERE user_id = ? AND event_id = ?");
        $delete->execute([$user_id, $event_id]);
        $status = "removed";

    } else {

        // toggle on and add rsvp 
        $insert = $pdo->prepare("
            INSERT INTO events_rsvp (user_id, event_id, status)
            VALUES (?, ?, 'going')
        ");
        $insert->execute([$user_id, $event_id]);
        $status = "added";
    }

    //get updated count from database 
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM events_rsvp 
        WHERE event_id = ? AND status = 'going'
    ");
    $countStmt->execute([$event_id]);
    $count = $countStmt->fetchColumn();

    echo json_encode([
        "status" => $status,
        "count" => $count
    ]);
    

} catch (Exception $e) {
    error_log("RSVP Error: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}
exit();

?>