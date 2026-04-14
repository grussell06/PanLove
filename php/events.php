<?php
session_start();

//if (!isset($_SESSION["user_id"])) {
//    header("Location: login.php");
//    exit();
//}
?>

<!doctype html>
<html>
<head>
    <title>Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/web.css">
</head>

<body class="container mt-4">

<h2>Upcoming Events</h2>
<button type="button" onclick="toggleCreateForm()" class="btn btn-primary mb-3">
    Create New Event
</button>

<!--create event form, hidden and shown when button is clicked-->
<div id="createEventForm" style="display:none;" class="mb-4 p-3 border rounded">

    <h5>Create Event</h5>

    <div class="mb-2">
        <input type="text" id="title" placeholder="Event Title">
    </div>

    <div class="mb-2">
        <textarea id="description" placeholder="Description"></textarea>
    </div>

    <div class="mb-2">
        <input type="date" id="event_date">
        <input type="time" id="event_time">
    </div>

    <div class="mb-2">
        <input type="text" id="location" placeholder="Location">
    </div>

    <div class="mb-2">
        <input type="text" id="chapter" placeholder="Organization">
    </div>

    <button type="button" onclick="handleCreateEvent()" class="btn btn-success">
        Save Event
    </button>

</div>

<!-- events load in eventsContainer -->
<div id="eventsContainer"></div>

<script src="../js/events.js"></script>

</body>
</html>