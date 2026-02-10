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
  <title>ChatRoom</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <h1>ChatRoom</h1>
  <div class="login">
    <h2>Login</h2>
    <p>This ChatRoom is the best example to demonstrate the concept of ChatBot and it's completely for begginners.</p>
    <form action="" method="post">

      <h3>UserName</h3>
      <input type="text" placeholder="Short Name" name="name">

      <h3>Mobile No:</h3>
      <input type="number" placeholder="with country code" min="1111111" max="999999999999999" name="phone">

      <button>Login / Register</button>

    </form>
  </div>
</body>
</html>