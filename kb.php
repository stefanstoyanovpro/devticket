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
    $category_id = (int)$_POST["category_id"];
    $problem = $_POST["problem"];
    $root_cause = $_POST["root_cause"];
    $resolution_steps = $_POST["resolution_steps"];
    $author_id = $_SESSION["user_id"];

    $stmt = $conn->prepare("INSERT INTO kb_articles (title, category_id, problem, root_cause, resolution_steps, author_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssi", $title, $category_id, $problem, $root_cause, $resolution_steps, $author_id);
    $stmt->execute();
    $stmt->close();

    header("Location: kb.php");
    exit;
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$articles = $conn->query("
    SELECT kb_articles.*, users.username, categories.name AS category_name
    FROM kb_articles
    LEFT JOIN users ON kb_articles.author_id = users.id
    LEFT JOIN categories ON kb_articles.category_id = categories.id
    ORDER BY kb_articles.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Knowledge Base - DevTicket</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .section-label { font-weight: bold; color: var(--text-muted); margin-top: 10px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.4px; }
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
        <h1>Knowledge Base</h1>

        <form method="POST" class="card">
            <input type="text" name="title" placeholder="Article title" required>
            <select name="category_id" required>
                <option value="">Category...</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <textarea name="problem" placeholder="Problem — what was observed/reported" required></textarea>
            <textarea name="root_cause" placeholder="Root cause — what actually caused it"></textarea>
            <textarea name="resolution_steps" placeholder="Resolution steps — how it was fixed" required></textarea>
            <button type="submit">Add Article</button>
        </form>

        <?php while ($a = $articles->fetch_assoc()): ?>
            <div class="article">
                <span class="category-tag"><?= htmlspecialchars($a['category_name'] ?? 'Uncategorized') ?></span>
                <h2 style="color:var(--text); text-transform:none; letter-spacing:0; font-size:18px;"><?= htmlspecialchars($a['title']) ?></h2>

                <div class="section-label">Problem</div>
                <p><?= nl2br(htmlspecialchars($a['problem'])) ?></p>

                <?php if ($a['root_cause']): ?>
                    <div class="section-label">Root Cause</div>
                    <p><?= nl2br(htmlspecialchars($a['root_cause'])) ?></p>
                <?php endif; ?>

                <div class="section-label">Resolution</div>
                <p><?= nl2br(htmlspecialchars($a['resolution_steps'])) ?></p>

                <small>By <?= htmlspecialchars($a['username'] ?? 'unknown') ?> on <?= $a['created_at'] ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
