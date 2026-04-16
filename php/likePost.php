<?php
session_start();
include 'dbFuncs.php';

if (isset($_GET['post_id']) && isset($_SESSION['user_id'])) {
    $post_id = $_GET['post_id'];
    $user_id = $_SESSION['user_id'];
    $pdo = connectDB();

    $check = $pdo->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $check->execute([$post_id, $user_id]);

    if ($check->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$post_id, $user_id]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);
    }

    // Fetch the new total count to send back to JavaScript
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    $countStmt->execute([$post_id]);
    $newCount = $countStmt->fetchColumn();

    // Return JSON instead of redirecting
    echo json_encode(['status' => 'success', 'newCount' => $newCount]);
    exit();
}
