<?php
session_start();
header('Content-Type: application/json');
include 'dbFuncs.php';

if (isset($_POST['submitPost']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];    
    $content = $_POST['content'];
    $image_name = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads";
        // Generate a unique name to avoid overwriting files with the same name
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            die("Error uploading your image.");
        }
    }

    try {
        $pdo = connectDB();
        
        $sql = "INSERT INTO posts (user_id, content, imageName) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$user_id, $content, $image_name])) {
            // 1. FETCH THE NAMES FROM THE DATABASE
            $userStmt = $pdo->prepare("SELECT fname, lname FROM users WHERE user_id = ?");
            $userStmt->execute([$user_id]);
            $user = $userStmt->fetch();

            // 2. RETURN DATA TO JAVASCRIPT
            echo json_encode([
                'status' => 'success',
                'content' => htmlspecialchars($content),
                'imageName' => $image_name,
                'fname' => $user['fname'], // Now pulling from $user variable
                'lname' => $user['lname'],
                'timestamp' => date("F j, Y, g:i a") // Sending current time for the feed
            ]);
            exit();
        }

    } catch (PDOException $e) {
        // Return errors as JSON so the AJAX call knows what went wrong
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
} else {
    // If the session is empty, let the AJAX know
    echo json_encode([
        'status' => 'error', 
        'message' => 'User not logged in.',
        'debug' => 'Session ID: ' . ($_SESSION['user_id'] ?? 'Empty')
    ]);
    exit();
}
?>
