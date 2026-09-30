<?php
require "db.php";
require "includes.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $category_id = (int)$_POST["category_id"];
    $impact = $_POST["impact"];
    $urgency = $_POST["urgency"];
    $priority = calculatePriority($impact, $urgency);
    $user_id = $_SESSION["user_id"];

    $stmt = $conn->prepare("INSERT INTO tickets (title, description, priority, category_id, impact, urgency, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissi", $title, $description, $priority, $category_id, $impact, $urgency, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$result = $conn->query("
    SELECT tickets.*, users.username, categories.name AS category_name
    FROM tickets
    LEFT JOIN users ON tickets.user_id = users.id
    LEFT JOIN categories ON tickets.category_id = categories.id
    ORDER BY 
        FIELD(tickets.priority, 'critical', 'high', 'medium', 'low'),
        tickets.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>DevTicket</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; background: #f4f5f7; }
        .ticket { background: white; border: 1px solid #ddd; padding: 14px; margin-bottom: 10px; border-radius: 6px; }
        .priority-critical { border-left: 5px solid #8e0000; }
        .priority-high { border-left: 5px solid #e74c3c; }
        .priority-medium { border-left: 5px solid #f39c12; }
        .priority-low { border-left: 5px solid #2ecc71; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; }
        .badge-critical { background: #8e0000; }
        .badge-high { background: #e74c3c; }
        .badge-medium { background: #f39c12; }
        .badge-low { background: #2ecc71; }
        .category-tag { background: #eee; padding: 2px 8px; border-radius: 4px; font-size: 12px; color: #333; }
        form { background: white; padding: 16px; border-radius: 6px; margin-bottom: 30px; border: 1px solid #ddd; }
        input, textarea, select { display: block; width: 100%; margin-bottom: 8px; padding: 6px; }
        .row { display: flex; gap: 10px; }
        .row > * { flex: 1; }
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
        <div class="row">
            <select name="category_id" required>
                <option value="">Category...</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <select name="impact">
                <option value="low">Impact: Low</option>
                <option value="medium" selected>Impact: Medium</option>
                <option value="high">Impact: High</option>
            </select>
            <select name="urgency">
                <option value="low">Urgency: Low</option>
                <option value="medium" selected>Urgency: Medium</option>
                <option value="high">Urgency: High</option>
            </select>
        </div>
        <button type="submit">Create Ticket</button>
    </form>

    <h2>Tickets</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="ticket priority-<?= $row['priority'] ?>">
            <span class="badge badge-<?= $row['priority'] ?>"><?= strtoupper($row['priority']) ?></span>
            <span class="category-tag"><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></span>
            <strong><?= htmlspecialchars($row['title']) ?></strong>
            (<?= $row['status'] ?>)
            <p><?= htmlspecialchars($row['description']) ?></p>
            <small>Impact: <?= $row['impact'] ?> · Urgency: <?= $row['urgency'] ?> · Created by <?= htmlspecialchars($row['username'] ?? 'unknown') ?> on <?= $row['created_at'] ?></small>
        </div>
    <?php endwhile; ?>
</body>
</html>
