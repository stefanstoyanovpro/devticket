<?php
require "db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Handle new ticket submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $priority = $_POST["priority"];
    $user_id = $_SESSION["user_id"];

    $stmt = $conn->prepare("INSERT INTO tickets (title, description, priority, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $priority, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

$result = $conn->query("
    SELECT tickets.*, users.username 
    FROM tickets 
    LEFT JOIN users ON tickets.user_id = users.id 
    ORDER BY tickets.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>DevTicket</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .ticket { border: 1px solid #ddd; padding: 12px; margin-bottom: 10px; border-radius: 6px; }
        .priority-high { border-left: 5px solid #e74c3c; }
        .priority-medium { border-left: 5px solid #f39c12; }
        .priority-low { border-left: 5px solid #2ecc71; }
        form { margin-bottom: 30px; }
        input, textarea, select { display: block; width: 100%; margin-bottom: 8px; padding: 6px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>DevTicket</h1>
        <div>Logged in as <strong><?= htmlspecialchars($_SESSION["username"]) ?></strong> | <a href="logout.php">Logout</a></div>
    </div>

    <form method="POST">
        <input type="text" name="title" placeholder="Ticket title" required>
        <textarea name="description" placeholder="Description"></textarea>
        <select name="priority">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
        </select>
        <button type="submit">Create Ticket</button>
    </form>

    <h2>Tickets</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="ticket priority-<?= $row['priority'] ?>">
            <strong><?= htmlspecialchars($row['title']) ?></strong> 
            (<?= $row['status'] ?>, <?= $row['priority'] ?> priority)
            <p><?= htmlspecialchars($row['description']) ?></p>
            <small>Created by <?= htmlspecialchars($row['username'] ?? 'unknown') ?> on <?= $row['created_at'] ?></small>
        </div>
    <?php endwhile; ?>
</body>
</html>
