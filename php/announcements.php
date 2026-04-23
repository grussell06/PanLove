<?php
session_start();

require_once "dbFuncs.php";
$pdo = connectDB();

$user_id = $_SESSION['user_id'] ?? null;
$is_exec = false;
$error = '';

if ($user_id) {
    $userStmt = $pdo->prepare("SELECT is_exec FROM users WHERE user_id = ?");
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch();
    if ($user && isset($user['is_exec'])) {
        $is_exec = (bool)$user['is_exec'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_announcement']) && $user_id && $is_exec) {
    $title = trim($_POST['ann_title'] ?? '');
    $content = trim($_POST['ann_content'] ?? '');

    if ($title === '' || $content === '') {
        $error = 'Please provide both a title and content for the announcement.';
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO announcements (user_id, ann_title, ann_content) VALUES (?, ?, ?)");
        $insertStmt->execute([$user_id, $title, $content]);
        header('Location: ./announcements.php');
        exit;
    }
}

$annStmt = $pdo->query("
    SELECT a.ann_id, a.ann_title, a.ann_content, a.timestamp, u.fname, u.lname
    FROM announcements a
    LEFT JOIN users u ON a.user_id = u.user_id
    ORDER BY a.timestamp DESC
");

$announcements = $annStmt->fetchAll();
?>

<!doctype html>
<html>
<head>
    <title>Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/announcements.css">
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Atma:wght@300;400;500;600;700&family=Original+Surfer&display=swap');
    </style>
</head>

<body class="container mt-4">

    <ul class="navBar">
        <li><a href="./homepage.php">Home</a></li>
        <li><a href="./events.php">Events</a></li>
        <li><a href="./announcements.php">Announcements</a></li>
        <li><a href="./myChapter.php">My Chapter</a></li>
        <?php
        if (isset($_SESSION['user_id'])) {
            echo '<li><a href="./profile.php">Profile</a></li>';
        } else {
            echo '<li><a href="./login.php">Login</a></li>';
        }
        ?>
    </ul>

    <div class="page-header">
        <h1>Announcements</h1>
        <p class="lead">Latest announcements!</p>
    </div>

    <!-- Only show the create announcement form if the user is an exec -->
    <?php if ($is_exec): ?>
        <div class="exec-actions mb-4">
            <button id="toggleCreate" class="btn btn-primary">Create Announcement</button>
        </div>

        <!--create announcement form hidden until button is clicked -->
        <div id="createForm" class="create-announcement-box hidden mb-4">
            <h2>New Announcement</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="./announcements.php">
                <input type="hidden" name="create_announcement" value="1">

                <div class="mb-3">
                    <label for="ann_title" class="form-label">Title</label>
                    <input type="text" id="ann_title" name="ann_title" class="form-control" maxlength="255" required>
                </div>

                <div class="mb-3">
                    <label for="ann_content" class="form-label">Content</label>
                    <textarea id="ann_content" name="ann_content" class="form-control" rows="5" required></textarea>
                </div>

                <button type="submit" class="btn btn-success">Post Announcement</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="announcements-list">

    <!--if no announcements show message saying no announcements --> 
        <?php if (count($announcements) === 0): ?>
            <div class="alert alert-secondary">
                No announcements have been posted yet.
            </div>
        <?php else: ?>
            <!-- Loop through announcements and display them in styled boxes -->
            <?php foreach ($announcements as $ann): ?>
                <div class="announcement-box mb-3">

                    <h3 class="announcement-title">
                        <?php echo htmlspecialchars($ann['ann_title']); ?>
                    </h3>

                    <p class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($ann['ann_content'])); ?>
                    </p>

                    <div class="announcement-meta text-muted">
                        Posted by
                        <?php echo htmlspecialchars($ann['fname'] ? $ann['fname'] . ' ' . $ann['lname'] : 'Unknown'); ?>
                        on <?php echo date('F j, Y, g:i a', strtotime($ann['timestamp'])); ?>
                    </div>

                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
<!--js for announcements? -->
    <script src="../js/announcements.js"></script>

</body>
</html>