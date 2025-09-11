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
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <style>
    body { background: #2c2e31; color: #e2e2e2; font-family: system-ui, -apple-system, sans-serif; }
    .card { width: 95%; max-width: 1200px; margin: 2.5rem auto; background: #323437; border: 1px solid #4a4d52; border-radius: 8px; padding: 2.5rem 2rem; }
    .title { font-size: 1.5rem; font-weight: 700; color: #e2e2e2; margin-bottom: 0.5rem; }
    .subtitle { color: #b5b5b5; margin-bottom: 2rem; }
    
    /* Set Name Input */
    .set-name-container { margin-bottom: 2rem; }
    .set-name-label { display: block; color: #e2e2e2; font-weight: 600; margin-bottom: 0.5rem; font-size: 1.1rem; }
    .set-name { width: 100%; padding: 0.75rem; border: 1px solid #4a4d52; border-radius: 6px; background: #3a3d42; color: #e2e2e2; font-size: 1rem; transition: border-color 0.2s, box-shadow 0.2s; }
    .set-name:focus { outline: none; border-color: #FFD700; box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2); }
    
    /* Table Styles */
    .create-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 2rem; background: #3a3d42; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); }
    .create-table thead th { background: #2c2e31; color: #FFD700; font-weight: 600; padding: 1rem; text-align: left; border-bottom: 1px solid #4a4d52; }
    .create-table thead th:first-child { border-top-left-radius: 8px; }
    .create-table thead th:last-child { border-top-right-radius: 8px; }
    .create-table tbody tr { transition: background 0.2s; }
    .create-table tbody tr:hover { background: #424549; }
    .create-table tbody td { padding: 1rem; border-bottom: 1px solid #4a4d52; vertical-align: top; }
    .create-table tbody tr:last-child td { border-bottom: none; }
    .create-table tbody tr:last-child td:first-child { border-bottom-left-radius: 8px; }
    .create-table tbody tr:last-child td:last-child { border-bottom-right-radius: 8px; }
    
    /* Input Styles */
    .table-input { width: 100%; padding: 0.75rem; border: 1px solid #4a4d52; border-radius: 6px; background: #2c2e31; color: #e2e2e2; resize: vertical; min-height: 3rem; font-family: inherit; transition: border-color 0.2s, box-shadow 0.2s; }
    .table-input:focus { outline: none; border-color: #FFD700; box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2); }
    
    /* Button Styles */
    .btn { background: #FFD700; color: #000000; font-weight: 600; padding: 0.7rem 2rem; border-radius: 6px; font-size: 1rem; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
    .btn:hover { background: #FFC107; transform: translateY(-1px); }
    .btn-secondary { background: #3a3d42; color: #e2e2e2; border: 1px solid #4a4d52; }
    .btn-secondary:hover { background: #424549; }
    
    /* Delete Row Button */
    .remove-btn { background: #dc2626; color: #ffffff; border: none; border-radius: 6px; padding: 0.5rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; }
    .remove-btn:hover { background: #b91c1c; transform: scale(1.05); }
    .remove-btn svg { width: 1rem; height: 1rem; }
    
    /* Action buttons container */
    .actions-container { display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap; }
    
    .success { color: #e2e2e2; margin-bottom: 1em; }
    .error { color: #e2e2e2; margin-bottom: 1em; background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1rem; }
  </style>
</head>
<body>
  <div class="card">
    <a href="index.php" class="btn btn-secondary" style="margin-bottom: 2rem;">← Back to Home</a>
    <div class="title">Create a New Flashcard Set</div>
    <div class="subtitle">Name your set and add as many cards as you like.</div>
    
    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form action="" method="post">
      <div class="set-name-container">
        <label class="set-name-label">Set Name</label>
        <input type="text" name="set_name" class="set-name" required pattern="[a-zA-Z0-9_]+" title="Letters, numbers, and underscores only" placeholder="e.g. biology101" value="<?= htmlspecialchars($set_name) ?>" />
      </div>
      
      <table class="create-table">
        <thead>
          <tr>
            <th style="width: 40%;">Term</th>
            <th style="width: 50%;">Definition</th>
            <th style="width: 10%; text-align: center;">Actions</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <tr>
            <td><textarea name="term[]" class="table-input" required placeholder="Enter the term..."></textarea></td>
            <td><textarea name="definition[]" class="table-input" required placeholder="Enter the definition..."></textarea></td>
            <td style="text-align: center;">
              <button type="button" class="remove-btn" onclick="removeRow(this)" title="Delete this row">
                <svg fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div class="actions-container">
        <button type="button" onclick="addRow()" class="btn btn-secondary">+ Add Row</button>
        <button type="submit" class="btn">Create Set</button>
      </div>
    </form>
  </div>
  <script>
    function removeRow(button) {
      const row = button.closest('tr');
      // Prevent removing the last row
      const tbody = document.getElementById('table-body');
      if (tbody.children.length > 1) {
        row.remove();
      } else {
        // If it's the last row, just clear the inputs
        const inputs = row.querySelectorAll('.table-input');
        inputs.forEach(input => input.value = '');
      }
    }
    
    function addRow() {
      const tableBody = document.getElementById('table-body');
      const row = document.createElement('tr');
      row.innerHTML = `
        <td><textarea name="term[]" class="table-input" required placeholder="Enter the term..."></textarea></td>
        <td><textarea name="definition[]" class="table-input" required placeholder="Enter the definition..."></textarea></td>
        <td style="text-align: center;">
          <button type="button" class="remove-btn" onclick="removeRow(this)" title="Delete this row">
            <svg fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
          </button>
        </td>
      `;
      tableBody.appendChild(row);
      
      // Focus on the first input of the new row
      const firstInput = row.querySelector('.table-input');
      firstInput.focus();
    }
  </script>
</body>
</html>


