<?php
session_start();
include 'dbFuncs.php';

if (isset($_GET['comment_id']) && isset($_SESSION['user_id'])) {
    $comment_id = $_GET['comment_id'];
    $user_id = $_SESSION['user_id'];
    $pdo = connectDB();

    // Security: Only delete if comment_id and user_id match
    $stmt = $pdo->prepare("DELETE FROM comments WHERE comment_id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);
}

header("Location: homepage.php");
exit();
