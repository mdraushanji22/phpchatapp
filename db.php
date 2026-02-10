<?php

$db = mysqli_connect("localhost", "root", "", "chatRoom");

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($db, "utf8mb4");

?>