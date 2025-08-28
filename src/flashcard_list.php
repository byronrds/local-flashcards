<?php
require_once "connect_db.php";

$set = isset($_GET['set']) ? $_GET['set'] : '';
$set = preg_replace('/[^a-zA-Z0-9_]/', '', $set); // sanitize

$terms = [];
$error = '';
if ($set) {
    try {
        $stmt = $db->query("SELECT term, definition FROM `$set`");
        $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = 'Could not load set: ' . htmlspecialchars($e->getMessage());
    }
} else {
    $error = 'No set specified.';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Flashcard Set</title>
    <link rel="stylesheet" href="./output.css">
    <style>
        .container { max-width: 900px; margin: 2.5rem auto; background: #fff; border-radius: 1rem; box-shadow: 0 4px 24px #0001; padding: 2.5rem 2rem; }
        .set-title { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem; }
        .card-list { display: flex; flex-wrap: wrap; gap: 1.5rem; }
        .flashcard { background: #f1f5f9; border-radius: 0.7rem; box-shadow: 0 2px 8px #6366f133; padding: 1.2rem 1.5rem; min-width: 220px; flex: 1 1 220px; }
        .term { font-weight: 600; color: #0ea5e9; margin-bottom: 0.5rem; }
        .def { color: #334155; }
        .back-btn { display:inline-block; margin-bottom:1.5rem; background:#f1f5f9; color:#1e293b; box-shadow:none; border-radius:6px; padding:10px 16px; text-decoration:none; border:none; font-weight:600; }
        .error { color: #b91c1c; margin-bottom: 1em; }
        @media (max-width: 600px) { .container { padding: 1.2rem 0.5rem; } .card-list { flex-direction: column; } }
    </style>
</head>
<body class="hero-bg min-h-screen">
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        <a href="edit_flashcards.php?set=<?= urlencode($set) ?>" class="btn" style="float:right;background:#6366f1;color:#fff;">Edit Set</a>
        <div class="set-title">Viewing Set: <span style="color:#6366f1;\"><?= htmlspecialchars(str_replace('set_', '', $set)) ?></span></div>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php elseif (empty($terms)): ?>
            <div class="error">No terms found in this set.</div>
        <?php else: ?>
            <div class="card-list">
                <?php foreach ($terms as $row): ?>
                    <div class="flashcard">
                        <div class="term"><?= htmlspecialchars($row['term']) ?></div>
                        <div class="def"><?= htmlspecialchars($row['definition']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
