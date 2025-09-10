<?php
require_once "../config/database_connection.php";

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
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
    <style>
        body { background: #2c2e31; color: #e2e2e2; }
        .container { width: 95%; max-width: 1200px; margin: 2.5rem auto; background: #323437; border: 1px solid #4a4d52; border-radius: 8px; padding: 2.5rem; }
        .set-title { font-size: 1.5rem; font-weight: 700; color: #e2e2e2; margin-bottom: 1.5rem; }
        .card-list { display: flex; flex-wrap: wrap; gap: 1.5rem; }
        .flashcard { background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1.2rem 1.5rem; min-width: 220px; flex: 1 1 220px; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3); }
        .flashcard:hover { border-color: #FFD700; background: #424549; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4); }
        .term { font-weight: 600; color: #FFD700; margin-bottom: 0.5rem; }
        .def { color: #b5b5b5; line-height: 1.5; }
        .back-btn { display: inline-block; margin-bottom: 1.5rem; background: #3a3d42; color: #e2e2e2; border: 1px solid #4a4d52; border-radius: 4px; padding: 10px 16px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
        .back-btn:hover { background: #424549; }
        .error { color: #e2e2e2; margin-bottom: 1em; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        <a href="edit_flashcards.php?set=<?= urlencode($set) ?>" class="btn" style="float:right;background:#FFD700;color:#000000;border:none;border-radius:4px;padding:10px 16px;font-weight:600;text-decoration:none;transition:background 0.2s;" onmouseover="this.style.background='#FFC107'" onmouseout="this.style.background='#FFD700'">Edit Set</a>
        <div class="set-title">Viewing Set: <span style="color:#FFD700;"><?= htmlspecialchars(str_replace('set_', '', $set)) ?></span></div>
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
