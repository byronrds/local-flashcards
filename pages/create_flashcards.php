<?php
require_once "../config/database_connection.php";

$success = false;
$error = '';
$set_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['set_name']) && !empty($_POST['term']) && !empty($_POST['definition'])) {
    $set_name_raw = trim($_POST['set_name']);
    // Only allow alphanumeric and underscores for table names
    $set_name = preg_replace('/[^a-zA-Z0-9_]/', '', $set_name_raw);
    if ($set_name === '') {
        $error = 'Invalid set name. Use only letters, numbers, and underscores.';
    } else {
        try {
            $table = "set_" . $set_name;
            $create = "CREATE TABLE IF NOT EXISTS `$table` (
                id INT AUTO_INCREMENT PRIMARY KEY,
                term TEXT NOT NULL,
                definition TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $db->exec($create);

            $insert = $db->prepare("INSERT INTO `$table` (term, definition) VALUES (?, ?)");
            $terms = $_POST['term'];
            $defs = $_POST['definition'];
            for ($i = 0; $i < count($terms); $i++) {
                if (trim($terms[$i]) !== '' && trim($defs[$i]) !== '') {
                    $insert->execute([trim($terms[$i]), trim($defs[$i])]);
                }
            }
            // Redirect to view the set after creation
            header("Location: flashcard_list.php?set=" . urlencode($table) . "&created=1");
            exit();
        } catch (PDOException $e) {
            $error = 'Error: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Flashcard Set</title>
  <link rel="stylesheet" href="../assets/css/output.css">
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
    <a href="index.php" class="btn" style="margin-bottom:1.2rem;display:inline-block;background:#f1f5f9;color:#1e293b;box-shadow:none;">← Back to Home</a>
    <div class="title">Create a New Flashcard Set</div>
    <div class="subtitle">Name your set and add as many cards as you like.</div>
    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form action="" method="post" class="mt-4">
      <label class="block mb-2 font-medium">Set Name</label>
      <input type="text" name="set_name" class="set-name" required pattern="[a-zA-Z0-9_]+" title="Letters, numbers, and underscores only" placeholder="e.g. biology101" />
      <table id="item-table" class="border-collapse border border-gray-400 w-full mb-2">
        <thead>
          <tr>
            <th class="border border-gray-300">Term</th>
            <th class="border border-gray-300">Definition</th>
            <th class="border border-gray-300"></th>
          </tr>
        </thead>
        <tbody id="table-body">
          <tr>
            <td class="border border-gray-300"><textarea name="term[]" class="input" required></textarea></td>
            <td class="border border-gray-300"><textarea name="definition[]" class="input" required></textarea></td>
            <td class="border border-gray-300 text-center"><button type="button" class="remove-btn" onclick="removeRow(this)">✖</button></td>
          </tr>
        </tbody>
      </table>
      <button type="button" onclick="addRow()" class="btn mb-2" style="background:#fbbf24;color:#1e293b;">Add Row</button>
      <button type="submit" class="btn">Create Set</button>
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


