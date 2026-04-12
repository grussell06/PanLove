<?php
session_start();
?>
<?php
require_once "dbFuncs.php";
$pdo = connectDB();

$sql = "SELECT id, title, description, event_date FROM events WHERE event_date >= NOW() ORDER BY event_date ASC LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$upcomingEvents = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <link rel="stylesheet" href="../css/web.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Homepage</title>
  </head>
  <body>
    <ul class = "navBar">
        <li><a href="./homepage.php">Home</a></li>
        <li><a href="./events.php">Events</a></li>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li><a href="./create_event.php">Create Event</a></li>
      <?php endif; ?>
        <li><a href="./announcements.php">Announcements</a></li>
        <li><a href="./myChapter.php">My Chapter</a></li>
        <?php
        if (isset($_SESSION['user_id'])) {
            // User is logged in
            echo '<li><a href="./profile.php">Profile</a></li>';
        } else {
            // User is not logged in
            echo '<li><a href="./login.php">Login</a></li>';
        }
        ?>
    </ul>
  <div class="content">
    <h1>Welcome to Panlove</h1>
    <p>This is where your main page data goes.</p>
  
  <section>
    <h2>Upcoming Events</h2>
    <?php if (!empty($upcomingEvents)): ?>
      <ul>
        <?php foreach ($upcomingEvents as $ev): ?>
          <li>
            <strong><?php echo htmlspecialchars($ev['title']); ?></strong>
            <br>
            <small><?php echo htmlspecialchars(date('F j, Y, g:i A', strtotime($ev['event_date']))); ?></small>
            <p><?php echo nl2br(htmlspecialchars($ev['description'])); ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No upcoming events.</p>
    <?php endif; ?>
  </section>

</div>






<div class="footer">
    <p>&copy; 2026 PanLove. All rights reserved.</p>

  </div>
  </body>
</html>