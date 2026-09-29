<?php
require "db.php";

// Handle new ticket submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $priority = $_POST["priority"];

    $stmt = $conn->prepare("INSERT INTO tickets (title, description, priority) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $priority);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

$result = $conn->query("SELECT * FROM tickets ORDER BY created_at DESC");
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
    </style>
</head>
<body>
    <h1>DevTicket</h1>

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
            <small>Created: <?= $row['created_at'] ?></small>
        </div>
    <?php endwhile; ?>
</body>
</html>
