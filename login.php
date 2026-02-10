<?php
include "db.php";
session_start();

if (isset($_POST["name"]) && isset($_POST["phone"])) {

    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);

    // Validation
    if (empty($name) || empty($phone)) {
        echo "<script>alert('Name and phone are required!')</script>";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $name)) {
        echo "<script>alert('Username must be 3-20 characters, alphanumeric only!')</script>";
    } elseif (!preg_match('/^[0-9]{7,15}$/', $phone)) {
        echo "<script>alert('Phone number must be 7-15 digits!')</script>";
    } else {
        // Check if user exists with both name and phone
        $stmt = mysqli_prepare($db, "SELECT * FROM `users` WHERE uname = ? AND phone = ?");
        mysqli_stmt_bind_param($stmt, "ss", $name, $phone);
        mysqli_stmt_execute($stmt);
        $rq = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($rq) == 1) {
            $_SESSION["userName"] = $name;
            $_SESSION["phone"] = $phone;
            header("location: index.php");
            exit();
        } else {
            // Check if phone is taken by another user
            $stmt = mysqli_prepare($db, "SELECT * FROM `users` WHERE phone = ?");
            mysqli_stmt_bind_param($stmt, "s", $phone);
            mysqli_stmt_execute($stmt);
            $rq = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($rq) == 1) {
                echo "<script>alert('" . htmlspecialchars($phone) . " is already taken by another person')</script>";
            } else {
                // Create new user
                $stmt = mysqli_prepare($db, "INSERT INTO `users`(`uname`, `phone`) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, "ss", $name, $phone);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION["userName"] = $name;
                    $_SESSION["phone"] = $phone;
                    header("location: index.php");
                    exit();
                } else {
                    echo "<script>alert('Registration failed. Please try again.')</script>";
                }
            }
        }
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ChatRoom - Login</title>
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body class="bg-gradient-primary">
  <div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
      <div class="col-md-5 col-lg-4">
        <!-- Login Card -->
        <div class="card shadow-lg border-0">
          <div class="card-body p-5">
            <!-- Header -->
            <div class="text-center mb-4">
              <i class="bi bi-chat-dots-fill text-primary display-4"></i>
              <h2 class="mt-3 mb-1">ChatRoom</h2>
              <p class="text-muted small">Real-time messaging made simple</p>
            </div>

            <!-- Form -->
            <form action="" method="post" id="loginForm" novalidate>
              <!-- Username Field -->
              <div class="mb-3">
                <label for="name" class="form-label fw-semibold">
                  <i class="bi bi-person-fill me-1"></i>Username
                </label>
                <input type="text" 
                       class="form-control form-control-lg" 
                       id="name" 
                       name="name" 
                       placeholder="Enter username (3-20 chars)"
                       pattern="[a-zA-Z0-9_]{3,20}"
                       required>
                <div class="form-text">Alphanumeric and underscore only</div>
              </div>

              <!-- Phone Field -->
              <div class="mb-4">
                <label for="phone" class="form-label fw-semibold">
                  <i class="bi bi-phone-fill me-1"></i>Mobile Number
                </label>
                <input type="tel" 
                       class="form-control form-control-lg" 
                       id="phone" 
                       name="phone" 
                       placeholder="Enter phone number"
                       pattern="[0-9]{7,15}"
                       required>
                <div class="form-text">7-15 digits, no country code needed</div>
              </div>

              <!-- Submit Button -->
              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="bi bi-box-arrow-in-right me-2"></i>Login / Register
                </button>
              </div>
            </form>

            <!-- Footer -->
            <div class="text-center mt-4">
              <small class="text-muted">
                New user? We'll create an account for you automatically.
              </small>
            </div>
          </div>
        </div>

        <!-- Info Card -->
        <div class="card mt-3 border-0 bg-transparent">
          <div class="card-body text-center text-white">
            <p class="mb-0 small">
              <i class="bi bi-shield-check me-1"></i>
              Secure messaging with end-to-end encryption
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5.3 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery 3.7.1 -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  
  <script>
    $(document).ready(function() {
      // Form validation
      $('#loginForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const phone = $('#phone').val().trim();
        
        if (!name || !phone) {
          e.preventDefault();
          alert('Please fill in all fields');
          return false;
        }
        
        if (!/^[a-zA-Z0-9_]{3,20}$/.test(name)) {
          e.preventDefault();
          alert('Username must be 3-20 alphanumeric characters');
          return false;
        }
        
        if (!/^[0-9]{7,15}$/.test(phone)) {
          e.preventDefault();
          alert('Phone number must be 7-15 digits');
          return false;
        }
      });
    });
  </script>
</body>
</html>