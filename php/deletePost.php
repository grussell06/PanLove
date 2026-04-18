<?php
session_start();
include 'dbFuncs.php';

if (isset($_GET['post_id']) && isset($_SESSION['user_id'])) {
    $post_id = $_GET['post_id'];
    $user_id = $_SESSION['user_id'];
    $pdo = connectDB();

    // Verify the user actually owns this post before deleting
    $stmt = $pdo->prepare("DELETE FROM posts WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    
    // all likes and comments will be deleted automatically!
}

header("Location: homepage.php");
exit();
