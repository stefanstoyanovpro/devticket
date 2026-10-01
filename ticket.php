<?php
require "db.php";
require "includes.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$ticket_id = (int)($_GET["id"] ?? 0);

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
    <link rel="stylesheet" href="style.css">
    <style>
        .note { border-left: 3px solid var(--border); padding: 8px 12px; margin-bottom: 10px; background: #0d1117; border-radius: 6px; }
    </style>
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

        <form method="POST" class="card">
            <textarea name="note" placeholder="Add an update..." required></textarea>
            <button type="submit">Add Note</button>
        </form>
    </div>
</body>
</html>
