<?php
session_start();
include 'dbFuncs.php';

if (isset($_POST['submitPost']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];    
    $content = $_POST['content'];
    $image_name = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        // Generate a unique name to avoid overwriting files with the same name
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            die("Error uploading your image.");
        }
    }

    try {
        $pdo = connectDB();
        
        // Update this line to include image_path if you added that column to your DB
        $sql = "INSERT INTO posts (user_id, content, imageName) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $content, $image_name]);

        header("Location: homepage.php");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    echo "Post failed. Debug info:<br>";
    echo "Submit button pressed: " . (isset($_POST['submitPost']) ? 'Yes' : 'No') . "<br>";
    echo "User ID in session: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Empty');
    exit();
}
?>
