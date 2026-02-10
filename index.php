<?php
session_start();
if (!isset($_SESSION["userName"]) || !isset($_SESSION["phone"])) {
    header("location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ChatRoom</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
  <script>
    const currentUserPhone = "<?php echo htmlspecialchars($_SESSION['phone']); ?>";
  </script>
</head>

<body>
  <div class="header">
    <h1>ChatRoom</h1>
    <div class="user-info">
      <span>Welcome, <strong><?= htmlspecialchars($_SESSION["userName"]) ?></strong></span>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>
  </div>

  <div class="chat">
    <div class="msg" id="msg-container">
    </div>
    <div class="input_msg">
      <input type="text" placeholder="Write your message here..." id="input_msg" maxlength="500">
      <button onclick="update()">Send</button>
    </div>
  </div>
</body>
<script src="js/script.js"></script>

</html>