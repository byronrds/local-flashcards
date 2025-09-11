<?php
require_once "../config/database_connection.php";

$set = isset($_GET['set']) ? $_GET['set'] : '';
$set = preg_replace('/[^a-zA-Z0-9_]/', '', $set); // sanitize

$terms = [];
$error = '';
$success = false;

if ($set) {
    // Handle delete set action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_set'])) {
        try {
            $db->exec("DROP TABLE `$set`");
            header("Location: index.php?deleted=1");
            exit();
        } catch (PDOException $e) {
            $error = 'Error deleting set: ' . htmlspecialchars($e->getMessage());
        }
    }
    // Handle update terms action
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['term']) && !empty($_POST['definition'])) {
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
  <link rel="stylesheet" href="../assets/css/output.css">
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <style>
    body { background: #2c2e31; color: #e2e2e2; font-family: system-ui, -apple-system, sans-serif; }
    .card { width: 95%; max-width: 1200px; margin: 2.5rem auto; background: #323437; border: 1px solid #4a4d52; border-radius: 8px; padding: 2.5rem 2rem; }
    .title { font-size: 1.5rem; font-weight: 700; color: #e2e2e2; margin-bottom: 0.5rem; }
    .subtitle { color: #b5b5b5; margin-bottom: 2rem; }
    
    /* Table Styles */
    .edit-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 2rem; background: #3a3d42; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); }
    .edit-table thead th { background: #2c2e31; color: #FFD700; font-weight: 600; padding: 1rem; text-align: left; border-bottom: 1px solid #4a4d52; }
    .edit-table thead th:first-child { border-top-left-radius: 8px; }
    .edit-table thead th:last-child { border-top-right-radius: 8px; }
    .edit-table tbody tr { transition: background 0.2s; }
    .edit-table tbody tr:hover { background: #424549; }
    .edit-table tbody td { padding: 1rem; border-bottom: 1px solid #4a4d52; vertical-align: top; }
    .edit-table tbody tr:last-child td { border-bottom: none; }
    .edit-table tbody tr:last-child td:first-child { border-bottom-left-radius: 8px; }
    .edit-table tbody tr:last-child td:last-child { border-bottom-right-radius: 8px; }
    
    /* Input Styles */
    .table-input { width: 100%; padding: 0.75rem; border: 1px solid #4a4d52; border-radius: 6px; background: #2c2e31; color: #e2e2e2; resize: vertical; min-height: 3rem; font-family: inherit; transition: border-color 0.2s, box-shadow 0.2s; }
    .table-input:focus { outline: none; border-color: #FFD700; box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2); }
    
    /* Button Styles */
    .btn { background: #FFD700; color: #000000; font-weight: 600; padding: 0.7rem 2rem; border-radius: 6px; font-size: 1rem; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
    .btn:hover { background: #FFC107; transform: translateY(-1px); }
    .btn-secondary { background: #3a3d42; color: #e2e2e2; border: 1px solid #4a4d52; }
    .btn-secondary:hover { background: #424549; }
    .btn-danger { background: #dc2626; color: #ffffff; }
    .btn-danger:hover { background: #b91c1c; }
    
    /* Delete Row Button */
    .remove-btn { background: #dc2626; color: #ffffff; border: none; border-radius: 6px; padding: 0.5rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; }
    .remove-btn:hover { background: #b91c1c; transform: scale(1.05); }
    .remove-btn svg { width: 1rem; height: 1rem; }
    
    /* Action buttons container */
    .actions-container { display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap; }
    .danger-zone { margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #4a4d52; }
    .danger-zone h3 { color: #dc2626; font-size: 1.1rem; margin-bottom: 0.5rem; }
    .danger-zone p { color: #b5b5b5; font-size: 0.9rem; margin-bottom: 1rem; }
    
    /* Modal Styles */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); }
    .modal-content { background-color: #323437; margin: 15% auto; padding: 2rem; border: 1px solid #4a4d52; border-radius: 8px; width: 90%; max-width: 480px; }
    .modal-header { margin-bottom: 1rem; }
    .modal-title { color: #dc2626; font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; }
    .modal-body { margin-bottom: 1.5rem; color: #e2e2e2; line-height: 1.5; }
    .modal-actions { display: flex; gap: 1rem; justify-content: flex-end; }
    
    .success { color: #e2e2e2; margin-bottom: 1em; }
    .error { color: #e2e2e2; margin-bottom: 1em; background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1rem; }
  </style>
</head>
<body>
  <div class="card">
    <a href="flashcard_list.php?set=<?= urlencode($set) ?>" class="btn btn-secondary" style="margin-bottom: 2rem;">← Back to Set</a>
    <div class="title">Edit Flashcard Set: <span style="color:#FFD700;"><?= htmlspecialchars(str_replace('set_', '', $set)) ?></span></div>
    <div class="subtitle">Update your terms and definitions below. Remove rows you don't want, or add new ones.</div>
    
    <?php if ($error): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form action="" method="post">
      <table class="edit-table">
        <thead>
          <tr>
            <th style="width: 40%;">Term</th>
            <th style="width: 50%;">Definition</th>
            <th style="width: 10%; text-align: center;">Actions</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <?php if (!empty($terms)): ?>
            <?php foreach ($terms as $row): ?>
              <tr>
                <td><textarea name="term[]" class="table-input" required><?= htmlspecialchars($row['term']) ?></textarea></td>
                <td><textarea name="definition[]" class="table-input" required><?= htmlspecialchars($row['definition']) ?></textarea></td>
                <td style="text-align: center;">
                  <button type="button" class="remove-btn" onclick="removeRow(this)" title="Delete this row">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td><textarea name="term[]" class="table-input" required></textarea></td>
              <td><textarea name="definition[]" class="table-input" required></textarea></td>
              <td style="text-align: center;">
                <button type="button" class="remove-btn" onclick="removeRow(this)" title="Delete this row">
                  <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                  </svg>
                </button>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      
      <div class="actions-container">
        <button type="button" onclick="addRow()" class="btn btn-secondary">+ Add Row</button>
        <button type="submit" class="btn">Save Changes</button>
      </div>
    </form>
    
    <div class="danger-zone">
      <h3>⚠️ Danger Zone</h3>
      <p>Permanently delete this entire flashcard set. This action cannot be undone.</p>
      <button type="button" onclick="showDeleteModal()" class="btn btn-danger">Delete Entire Set</button>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">⚠️ Confirm Deletion</h2>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to permanently delete the entire flashcard set "<strong><?= htmlspecialchars(str_replace('set_', '', $set)) ?></strong>"?</p>
        <p>This will delete all <?= count($terms) ?> flashcard(s) in this set. This action cannot be undone.</p>
      </div>
      <div class="modal-actions">
        <button type="button" onclick="hideDeleteModal()" class="btn btn-secondary">Cancel</button>
        <form method="post" style="display: inline;">
          <input type="hidden" name="delete_set" value="1">
          <button type="submit" class="btn btn-danger">Yes, Delete Set</button>
        </form>
      </div>
    </div>
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
        <td><textarea name="term[]" class="table-input" required></textarea></td>
        <td><textarea name="definition[]" class="table-input" required></textarea></td>
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
    
    function showDeleteModal() {
      document.getElementById('deleteModal').style.display = 'block';
    }
    
    function hideDeleteModal() {
      document.getElementById('deleteModal').style.display = 'none';
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
      const modal = document.getElementById('deleteModal');
      if (event.target === modal) {
        hideDeleteModal();
      }
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        hideDeleteModal();
      }
    });
  </script>
</body>
</html>
