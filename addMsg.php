<?php
session_start();
include "db.php";
include "pusher.php";

header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (!isset($_SESSION["phone"])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

if (!isset($_GET["msg"]) || empty(trim($_GET["msg"]))) {
    echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty']);
    exit();
}

$msg = trim($_GET["msg"]);
$phone = $_SESSION["phone"];

// Validate message length
if (strlen($msg) > 500) {
    echo json_encode(['status' => 'error', 'message' => 'Message too long (max 500 chars)']);
    exit();
}

// Verify user exists
$stmt = mysqli_prepare($db, "SELECT uname FROM `users` WHERE phone = ?");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($db)]);
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $phone);
mysqli_stmt_execute($stmt);
$rq = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($rq) == 1) {
    $user = mysqli_fetch_assoc($rq);
    $userName = $user['uname'];

    // Insert message (created_at uses DEFAULT CURRENT_TIMESTAMP)
    $stmt = mysqli_prepare($db, "INSERT INTO `msg`(`phone`, `msg`) VALUES (?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($db)]);
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $phone, $msg);

    if (mysqli_stmt_execute($stmt)) {
        // Trigger Pusher event for real-time updates (wrapped in try-catch)
        try {
            $data = [
                'phone' => $phone,
                'name' => $userName,
                'message' => $msg,
                'time' => date('H:i')
            ];
            $pusher->trigger('chat-channel', 'new-message', $data);
        } catch (Exception $e) {
            // Log error but don't fail the message sending
            error_log('Pusher error: ' . $e->getMessage());
        }

        echo json_encode(['status' => 'success', 'message' => 'Message sent']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert message: ' . mysqli_stmt_error($stmt)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}
?>