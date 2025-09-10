<?php
// Minimal setup helper: show manual config and allow user to verify once they've created src/db_config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_CONFIG_FILE', __DIR__ . '/db_config.php');

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // User clicked "I've created the file" — verify it exists and contains required values
    if (!file_exists(DB_CONFIG_FILE)) {
        $error = "Configuration file not found. Please create <code>config/db_config.php</code> first.";
    } else {
        $config = @include DB_CONFIG_FILE;
        if (!is_array($config) || empty($config['host']) || empty($config['user']) || empty($config['name'])) {
            $error = "Configuration file found but looks invalid. Please ensure it returns an array with 'host','user','pass','name'.";
        } else {
            // Try to connect and create the 'user' table if it doesn't exist
            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4";
                $pdo = new PDO($dsn, $config['user'], $config['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $pdo->exec("CREATE TABLE IF NOT EXISTS user (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                // All good — redirect to app
                header('Location: ../pages/index.php');
                exit();
            } catch (PDOException $e) {
                $error = 'Database error: ' . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Local Flashcards — Manual DB Config</title>
    <style>
        body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; }
        .card { max-width:720px; margin:48px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
        pre { background:#f4f4f4; padding:16px; border-radius:6px; overflow:auto; }
        .error { color:#b00; margin:12px 0; }
        .btn { display:inline-block; padding:10px 16px; background:#0366d6; color:#fff; border-radius:6px; text-decoration:none; border:none; cursor:pointer; }
        .note { color:#555; font-size:0.95rem; }
    </style>
</head>
<body>
<div class="card">
    <h1>Manual DB config</h1>
    <p class="note">PHP couldn't write the config file automatically, or you prefer to create it manually. Create <code>config/db_config.php</code> with the contents below and then click <strong>I've created the file</strong>.</p>

    <pre><?php echo htmlspecialchars("<?php\nreturn [\n    'host' => 'localhost',\n    'user' => 'root',\n    'pass' => '',\n    'name' => 'local_flashcards',\n];\n"); ?></pre>

    <p class="note">After creating the file, set secure permissions if necessary (example: <code>chmod 644 config/db_config.php</code>).</p>

    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

    <form method="post">
        <button type="submit" class="btn">I've created the file — verify and continue</button>
    </form>

    <p style="margin-top:18px" class="note">If verification fails, double-check the file path and that it returns an array like the example above. Don't refresh this page while copying: click the button only once after creating the file.</p>
</div>
</body>
</html>
