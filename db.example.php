<?php
$host = "localhost";
$user = "devticket_user";
$pass = "Password123";
$db   = "devticket";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
