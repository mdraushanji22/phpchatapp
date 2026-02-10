<?php
session_start();
include "db.php";

if (!isset($_SESSION["phone"])) {
    exit();
}

$currentPhone = $_SESSION["phone"];

// Get only messages from the last 24 hours (for new user experience)
// Check if created_at column exists, if not use id for ordering
$checkColumn = mysqli_query($db, "SHOW COLUMNS FROM `msg` LIKE 'created_at'");
$hasCreatedAt = mysqli_num_rows($checkColumn) > 0;

// Always show messages in chronological order (oldest first)
// This ensures sent messages appear at bottom, not top
if ($hasCreatedAt) {
    $q = "SELECT m.*, u.uname FROM `msg` m 
          JOIN `users` u ON m.phone = u.phone 
          WHERE m.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
          ORDER BY m.id ASC LIMIT 50";
} else {
    // Fallback if created_at doesn't exist yet
    $q = "SELECT m.*, u.uname FROM `msg` m 
          JOIN `users` u ON m.phone = u.phone 
          ORDER BY m.id ASC LIMIT 50";
}

if ($rq = mysqli_query($db, $q)) {
    $messages = [];
    while ($data = mysqli_fetch_assoc($rq)) {
        $messages[] = $data;
    }

    foreach ($messages as $data) {
        $isSender = ($data["phone"] == $currentPhone);
        $displayName = htmlspecialchars($data["uname"]);
        $messageText = htmlspecialchars($data["msg"]);
        
        // Bootstrap styled message bubble
        $bgClass = $isSender ? 'bg-primary text-white' : 'bg-light border';
        $alignClass = $isSender ? 'ms-auto' : 'me-auto';
        $usernameColor = $isSender ? 'text-white-50' : 'text-primary';
?>
<div class="message p-2">
    <div class="d-flex <?= $isSender ? 'justify-content-end' : 'justify-content-start' ?>">
        <div class="message-bubble <?= $bgClass ?> <?= $alignClass ?> rounded-3 p-3 mb-2 shadow-sm" style="max-width: 75%;">
            <div class="message-header mb-1">
                <span class="username fw-bold <?= $usernameColor ?>" style="font-size: 0.85rem;">
                    <?= $displayName ?>
                </span>
            </div>
            <div class="message-text" style="word-wrap: break-word;"><?= $messageText ?></div>
        </div>
    </div>
</div>
<?php
    }

    if (empty($messages)) {
        echo "<div class='empty-chat d-flex align-items-center justify-content-center h-100 text-muted'>
                <div class='text-center'>
                    <i class='bi bi-chat-square-text display-4 mb-3'></i>
                    <h5>No messages yet</h5>
                    <p class='small'>Start the conversation by sending a message!</p>
                </div>
              </div>";
    }
}
?>