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

$stats = $conn->query("
    SELECT
        COUNT(*) AS total,
        SUM(status = 'open') AS open_count,
        SUM(priority = 'critical') AS critical_count,
        SUM(priority = 'high') AS high_count
    FROM tickets
")->fetch_assoc();

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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-nav">
        <span class="brand">DevTicket</span>
        <div>
            <a href="index.php">Tickets</a>
            <a href="kb.php">Knowledge Base</a>
            <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION["username"]) ?>)</a>
        </div>
    </div>

    <div class="wrap">
        <div class="stats-row">
            <div class="stat-card">
                <div class="label">Total Tickets</div>
                <div class="value"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Open</div>
                <div class="value"><?= $stats['open_count'] ?></div>
            </div>
            <div class="stat-card critical">
                <div class="label">Critical</div>
                <div class="value"><?= $stats['critical_count'] ?></div>
            </div>
            <div class="stat-card high">
                <div class="label">High Priority</div>
                <div class="value"><?= $stats['high_count'] ?></div>
            </div>
        </div>

        <form method="POST" class="card">
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
                <strong><a href="ticket.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></strong>
                (<?= $row['status'] ?>)
                <p><?= htmlspecialchars($row['description']) ?></p>
                <small>Impact: <?= $row['impact'] ?> · Urgency: <?= $row['urgency'] ?> · Created by <?= htmlspecialchars($row['username'] ?? 'unknown') ?> on <?= $row['created_at'] ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
