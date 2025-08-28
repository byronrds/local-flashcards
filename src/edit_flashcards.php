<?php
require_once "connect_db.php";

$set = isset($_GET['set']) ? $_GET['set'] : '';
$set = preg_replace('/[^a-zA-Z0-9_]/', '', $set); // sanitize

$terms = [];
$error = '';
$success = false;

if ($set) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['term']) && !empty($_POST['definition'])) {
        try {
            // Remove all existing rows
            $db->exec("DELETE FROM `$set`");
            // Insert new rows
            $insert = $db->prepare("INSERT INTO `$set` (term, definition) VALUES (?, ?)");
            $terms_post = $_POST['term'];
            $defs_post = $_POST['definition'];
            for ($i = 0; $i < count($terms_post); $i++) {
                if (trim($terms_post[$i]) !== '' && trim($defs_post[$i]) !== '') {
                    $insert->execute([trim($terms_post[$i]), trim($defs_post[$i])]);
                }
            }
            // Redirect to avoid resubmission
            header("Location: flashcard_list.php?set=" . urlencode($set) . "&updated=1");
            exit();
        } catch (PDOException $e) {
            $error = 'Error: ' . htmlspecialchars($e->getMessage());
        }
    }
    // Fetch current terms
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
  <title>Edit Flashcard Set</title>
  <link rel="stylesheet" href="./output.css">
  <style>
    .card { width: 98vw; max-width: none; margin: 2.5rem auto; background: #fff; border-radius: 1rem; box-shadow: 0 4px 24px #0001; padding: 2.5rem 2rem; }
    .title { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
    .subtitle { color: #475569; margin-bottom: 1.5rem; }
    .input, .set-name { width: 100%; padding: 0.5em; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 1em; }
    .input, .set-name, textarea { box-sizing: border-box; }
    .input, textarea { resize: vertical; min-height: 2.2em; max-height: 8em; white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word; }
    .btn { background: linear-gradient(90deg, #6366f1 0%, #0ea5e9 100%); color: #fff; font-weight: 600; padding: 0.7rem 2rem; border-radius: 0.5rem; font-size: 1.1rem; border: none; cursor: pointer; transition: background 0.2s; }
    .btn:hover { background: linear-gradient(90deg, #4f46e5 0%, #0284c7 100%); }
    .remove-btn { color: #e11d48; font-size: 1.2rem; border: none; background: none; cursor: pointer; }
    .success { color: #059669; margin-bottom: 1em; }
    .error { color: #b91c1c; margin-bottom: 1em; }
    @media (max-width: 600px) { .card { padding: 1.2rem 0.5rem; } }
  </style>
</head>
<body class="hero-bg min-h-screen">
  <div class="card">
    <a href="flashcard_list.php?set=<?= urlencode($set) ?>" class="btn" style="margin-bottom:1.2rem;display:inline-block;background:#f1f5f9;color:#1e293b;box-shadow:none;">← Back to Set</a>
    <div class="title">Edit Flashcard Set: <span style="color:#6366f1;\"><?= htmlspecialchars(str_replace('set_', '', $set)) ?></span></div>
    <div class="subtitle">Update your terms and definitions below. Remove rows you don't want, or add new ones.</div>
    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form action="" method="post" class="mt-4">
      <table id="item-table" class="border-collapse border border-gray-400 w-full mb-2">
        <thead>
          <tr>
            <th class="border border-gray-300">Term</th>
            <th class="border border-gray-300">Definition</th>
            <th class="border border-gray-300"></th>
          </tr>
        </thead>
        <tbody id="table-body">
          <?php if (!empty($terms)): ?>
            <?php foreach ($terms as $row): ?>
              <tr>
                <td class="border border-gray-300"><textarea name="term[]" class="input" required><?= htmlspecialchars($row['term']) ?></textarea></td>
                <td class="border border-gray-300"><textarea name="definition[]" class="input" required><?= htmlspecialchars($row['definition']) ?></textarea></td>
                <td class="border border-gray-300 text-center"><button type="button" class="remove-btn" onclick="removeRow(this)">✖</button></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td class="border border-gray-300"><textarea name="term[]" class="input" required></textarea></td>
              <td class="border border-gray-300"><textarea name="definition[]" class="input" required></textarea></td>
              <td class="border border-gray-300 text-center"><button type="button" class="remove-btn" onclick="removeRow(this)">✖</button></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <button type="button" onclick="addRow()" class="btn mb-2" style="background:#fbbf24;color:#1e293b;">Add Row</button>
      <button type="submit" class="btn">Save Changes</button>
    </form>
  </div>
  <script>
    function removeRow(button) {
      button.closest('tr').remove();
    }
    function addRow() {
      const tableBody = document.getElementById('table-body');
      const row = document.createElement('tr');
      row.innerHTML = `
        <td class="border border-gray-300"><textarea name="term[]" class="input" required></textarea></td>
        <td class="border border-gray-300"><textarea name="definition[]" class="input" required></textarea></td>
        <td class="border border-gray-300 text-center"><button type="button" class="remove-btn" onclick="removeRow(this)">✖</button></td>
      `;
      tableBody.appendChild(row);
    }
  </script>
</body>
</html>
