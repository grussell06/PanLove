<?php
require_once "dbFuncs.php";

$pdo = connectDB();

$stmt = $pdo->prepare("
    INSERT INTO events (title, description, event_date, event_time, location, chapter)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $_POST["title"],
    $_POST["description"],
    $_POST["event_date"],
    $_POST["event_time"],
    $_POST["location"],
    $_POST["chapter"]
]);

echo "Success";
?>