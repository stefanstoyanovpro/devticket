<?php
require "db.php";
require "includes.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (!isValidUsername($username) || !isValidPassword($password)) {
        $error = "Username must be 3+ chars, password 6+ chars.";
    } else {
        $hash = hashPassword($password);

        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            $stmt->execute();
            $stmt->close();
            header("Location: login.php");
            exit;
        } catch (mysqli_sql_exception $e) {
            $error = "Username already taken.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - DevTicket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-nav">
        <span class="brand">DevTicket</span>
    </div>
    <div class="wrap" style="max-width:380px;">
        <h1>Register</h1>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="POST" class="card">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
