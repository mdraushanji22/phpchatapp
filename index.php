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
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
  <!-- Pusher 8.4.0 -->
  <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
  <!-- jQuery 3.7.1 -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    const currentUserPhone = "<?php echo htmlspecialchars($_SESSION['phone']); ?>";
  </script>
</head>

<body class="bg-light">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="#">
        <strong>ChatRoom</strong>
      </a>
      <div class="d-flex align-items-center text-white">
        <span class="me-3">Welcome, <strong><?= htmlspecialchars($_SESSION["userName"]) ?></strong></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Chat Container -->
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <!-- Chat Card -->
        <div class="card shadow">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 text-primary">Messages</h5>
          </div>
          
          <!-- Messages Area -->
          <div class="card-body p-0">
            <div class="msg" id="msg-container" style="height: 400px; overflow-y: auto;">
            </div>
          </div>
          
          <!-- Input Area -->
          <div class="card-footer bg-white border-top">
            <div class="input-group">
              <input type="text" 
                     class="form-control" 
                     placeholder="Type your message..." 
                     id="input_msg" 
                     maxlength="500"
                     autocomplete="off">
              <button class="btn btn-primary" type="button" id="send-btn">
                <i class="bi bi-send"></i> Send
              </button>
            </div>
            <small class="text-muted mt-1 d-block" id="char-count">0/500</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5.3 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="js/script.js"></script>
</body>

</html>