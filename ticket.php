<?php
require "db.php";
require "includes.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$ticket_id = (int)($_GET["id"] ?? 0);

// Add a note
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["note"])) {
    $note = trim($_POST["note"]);
    if ($note !== "") {
        $stmt = $conn->prepare("INSERT INTO ticket_activity (ticket_id, user_id, note) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $ticket_id, $_SESSION["user_id"], $note);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: ticket.php?id=" . $ticket_id);
    exit;
}

$stmt = $conn->prepare("
    SELECT tickets.*, users.username, categories.name AS category_name
    FROM tickets
    LEFT JOIN users ON tickets.user_id = users.id
    LEFT JOIN categories ON tickets.category_id = categories.id
    WHERE tickets.id = ?
");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ticket) {
    die("Ticket not found.");
}

$stmt = $conn->prepare("
    SELECT ticket_activity.*, users.username
    FROM ticket_activity
    LEFT JOIN users ON ticket_activity.user_id = users.id
    WHERE ticket_id = ?
    ORDER BY created_at ASC
");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$activity = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($ticket['title']) ?> - DevTicket</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; background: #f4f5f7; }
        .card { background: white; border: 1px solid #ddd; padding: 16px; border-radius: 6px; margin-bottom: 20px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; }
        .badge-critical { background: #8e0000; }
        .badge-high { background: #e74c3c; }
        .badge-medium { background: #f39c12; }
        .badge-low { background: #2ecc71; }
        .note { border-left: 3px solid #ccc; padding: 8px 12px; margin-bottom: 10px; background: #fafafa; }
        .note small { color: #888; }
        textarea, button { width: 100%; padding: 8px; margin-top: 8px; }
        a { color: #2563eb; }
    </style>
</head>
<body>
    <p><a href="index.php">&larr; Back to all tickets</a></p>

    <div class="card">
        <span class="badge badge-<?= $ticket['priority'] ?>"><?= strtoupper($ticket['priority']) ?></span>
        <h1><?= htmlspecialchars($ticket['title']) ?></h1>
        <p><?= htmlspecialchars($ticket['description']) ?></p>
        <small>
            Category: <?= htmlspecialchars($ticket['category_name'] ?? 'Uncategorized') ?> ·
            Impact: <?= $ticket['impact'] ?> · Urgency: <?= $ticket['urgency'] ?> ·
            Status: <?= $ticket['status'] ?> ·
            Reported by <?= htmlspecialchars($ticket['username'] ?? 'unknown') ?> on <?= $ticket['created_at'] ?>
        </small>
    </div>

    <h2>Activity</h2>
    <?php while ($note = $activity->fetch_assoc()): ?>
        <div class="note">
            <?= htmlspecialchars($note['note']) ?>
            <br><small><?= htmlspecialchars($note['username'] ?? 'unknown') ?> — <?= $note['created_at'] ?></small>
        </div>
    <?php endwhile; ?>

    <form method="POST">
        <textarea name="note" placeholder="Add an update..." required></textarea>
        <button type="submit">Add Note</button>
    </form>
</body>
</html>
