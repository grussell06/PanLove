<?php
session_start();
include 'dbFuncs.php';

if (isset($_POST['comment_text'], $_POST['post_id']) && isset($_SESSION['user_id'])) {
    $pdo = connectDB();
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];
    $text = $_POST['comment_text'];

    // Insert the comment
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $text]);

    // Fetch the user's name so we can display it instantly
    $userStmt = $pdo->prepare("SELECT fname FROM users WHERE user_id = ?");
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch();

    // Return JSON data
    echo json_encode([
        'status' => 'success',
        'fname' => $user['fname'],
        'comment_text' => $text
    ]);
    exit();
}
