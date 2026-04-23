<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

require_once "dbFuncs.php";
$pdo = connectDB();

// Get current user's sorority
$stmt = $pdo->prepare("SELECT sorority FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

$sorority = $user['sorority'];

// Get all users in the same sorority
$stmt = $pdo->prepare("SELECT fname, lname, email FROM users WHERE sorority = ? ORDER BY fname, lname");
$stmt->execute([$sorority]);
$members = $stmt->fetchAll();

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <link rel="stylesheet" href="../css/web.css">
    <link rel="stylesheet" href="../css/myChapter.css">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>My Chapter</title>

    <style>
      @import url('https://fonts.googleapis.com/css2?family=Emilys+Candy&display=swap');
    </style>
  </head>
  <body>
    <ul class="navBar">
        <li><a href="./homepage.php">Home</a></li>
        <li><a href="./events.php">Events</a></li>
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
        <h1>My Chapter: <?php echo htmlspecialchars($sorority); ?></h1>
        <div class="chapter-members">
            <?php if (count($members) > 0): ?>
                <ul class="member-list">
                    <?php foreach ($members as $member): ?>
                        <li class="member-item">
                            <div class="member-name"><?php echo htmlspecialchars($member['fname'] . ' ' . $member['lname']); ?></div>
                            <div class="member-email"><?php echo htmlspecialchars($member['email']); ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No members found in your chapter.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>