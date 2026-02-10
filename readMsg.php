<?php
session_start();
include "db.php";

if (!isset($_SESSION["phone"])) {
    exit();
}

$currentPhone = $_SESSION["phone"];

// Get messages with user info
$q = "SELECT m.*, u.uname FROM `msg` m 
      JOIN `users` u ON m.phone = u.phone 
      ORDER BY m.id DESC LIMIT 50";

if ($rq = mysqli_query($db, $q)) {
    $messages = [];
    while ($data = mysqli_fetch_assoc($rq)) {
        $messages[] = $data;
    }
    // Reverse to show oldest first
    $messages = array_reverse($messages);

    foreach ($messages as $data) {
        $isSender = ($data["phone"] == $currentPhone);
        $class = $isSender ? "sender" : "";
        $displayName = htmlspecialchars($data["uname"]);
        $messageText = htmlspecialchars($data["msg"]);
        $time = isset($data["created_at"]) ? date('H:i', strtotime($data["created_at"])) : '';
?>
<div class="message <?= $class ?>">
    <div class="message-header">
        <span class="username"><?= $displayName ?></span>
        <span class="time"><?= $time ?></span>
    </div>
    <div class="message-text"><?= $messageText ?></div>
</div>
<?php
    }

    if (empty($messages)) {
        echo "<div class='empty-chat'><h3>Chat is empty at this moment!!</h3></div>";
    }
}
?>