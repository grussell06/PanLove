<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

require_once "dbFuncs.php";
$pdo = connectDB();

// Handle form submission for updating profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $grade = $_POST['grade'];
    $sorority = $_POST['sorority'];

    // Check if password change is requested
    $updatePassword = false;
    if (!empty($_POST['new_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (password_verify($currentPassword, $user['password'])) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePassword = true;
            } else {
                $errorMsg = "New passwords do not match.";
            }
        } else {
            $errorMsg = "Current password is incorrect.";
        }
    }

    if (!isset($errorMsg)) {
    try {
        if ($updatePassword) {
            $sql = "UPDATE users SET fname = ?, lname = ?, email = ?, grade = ?, sorority = ?, password = ? WHERE user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fname, $lname, $email, $grade, $sorority, $hashedPassword, $_SESSION['user_id']]);
        } else {
            $sql = "UPDATE users SET fname = ?, lname = ?, email = ?, grade = ?, sorority = ? WHERE user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fname, $lname, $email, $grade, $sorority, $_SESSION['user_id']]);
        }
        $successMsg = "Profile updated successfully!";
        
        // Refresh $user data so the form shows the NEW info immediately
        $stmt = $pdo->prepare("SELECT fname, lname, email, grade, sorority FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
    } catch (PDOException $e) {
        $errorMsg = "Database Error: " . $e->getMessage();
    }
}
}

// Fetch current user data
$stmt = $pdo->prepare("SELECT fname, lname, email, grade, sorority FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found in database for ID: " . htmlspecialchars($_SESSION['user_id']));
}

// After $user = $stmt->fetch();
if (!$user) {
    // Redirect or show a clean error if user isn't found
    die("User not found. Please log in again.");
}
?>



<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <link rel="stylesheet" href="../css/profile.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Profile</title>
</head>
<body class="profile-body">
    <ul class="navBar">
        <li><a href="./homepage.php">Home</a></li>
        <li><a href="./events.php">Events</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="./createEvent.php">Create Event</a></li>
        <?php endif; ?>
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

    <div class="container profile-container">
        <h2>My Profile</h2>

        <?php if (isset($successMsg)): ?>
            <div class="alert alert-success"><?php echo $successMsg; ?></div>
        <?php endif; ?>

        <?php if (isset($errorMsg)): ?>
            <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <form action="./profile.php" method="POST">
            <div class="mb-3">
                <label for="fname" class="form-label">First Name:</label>
                <input type="text" class="form-control" id="fname" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="lname" class="form-label">Last Name:</label>
                <input type="text" class="form-control" id="lname" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="grade" class="form-label">Grade:</label>
                <select class="form-control" id="grade" name="grade" required>
                    <option value="Freshman" <?php if ($user['grade'] == 'Freshman') echo 'selected'; ?>>Freshman</option>
                    <option value="Sophomore" <?php if ($user['grade'] == 'Sophomore') echo 'selected'; ?>>Sophomore</option>
                    <option value="Junior" <?php if ($user['grade'] == 'Junior') echo 'selected'; ?>>Junior</option>
                    <option value="Senior" <?php if ($user['grade'] == 'Senior') echo 'selected'; ?>>Senior</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="sorority" class="form-label">Sorority:</label>
                <select class="form-control" id="sorority" name="sorority" required>
                    <option value="Alpha Omicron Pi" <?php if ($user['sorority'] == 'Alpha Omicron Pi') echo 'selected'; ?>>Alpha Omicron Pi</option>
                    <option value="Delta Gamma" <?php if ($user['sorority'] == 'Delta Gamma') echo 'selected'; ?>>Delta Gamma</option>
                    <option value="Kappa Delta" <?php if ($user['sorority'] == 'Kappa Delta') echo 'selected'; ?>>Kappa Delta</option>
                    <option value="Alpha Delta Pi" <?php if ($user['sorority'] == 'Alpha Delta Pi') echo 'selected'; ?>>Alpha Delta Pi</option>
                    <option value="Alpha Gamma Delta" <?php if ($user['sorority'] == 'Alpha Gamma Delta') echo 'selected'; ?>>Alpha Gamma Delta</option>
                    <option value="Delta Zeta" <?php if ($user['sorority'] == 'Delta Zeta') echo 'selected'; ?>>Delta Zeta</option>
                    <option value="Phi Mu" <?php if ($user['sorority'] == 'Phi Mu') echo 'selected'; ?>>Phi Mu</option>
                    <option value="Zeta Tau Alpha" <?php if ($user['sorority'] == 'Zeta Tau Alpha') echo 'selected'; ?>>Zeta Tau Alpha</option>
                </select>
            </div>

            <h4 class="mt-4">Change Password (Optional)</h4>
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password:</label>
                <input type="password" class="form-control" id="current_password" name="current_password">
            </div>

            <div class="mb-3">
                <label for="new_password" class="form-label">New Password:</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
