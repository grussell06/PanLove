<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!doctype html>
<html>
<head>
    <title>Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2>Upcoming Events</h2>

<div id="eventsContainer"></div>

<script src="js/events.js"></script>

</body>
</html>