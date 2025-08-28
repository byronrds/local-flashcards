<?php
// setup.php: Initial database setup for Local Flashcards
// Prompts for DB credentials, creates DB/tables, and saves config

session_start();

// Path to config file
define('DB_CONFIG_FILE', __DIR__ . '/db_config.php');

// If config already exists and is filled, redirect to index
if (file_exists(DB_CONFIG_FILE)) {
    $config = require DB_CONFIG_FILE;
    if (!empty($config['host']) && !empty($config['user']) && !empty($config['name'])) {
        header('Location: index.php');
        exit();
    }
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['db_host'] ?? '';
    $user = $_POST['db_user'] ?? '';
    $pass = $_POST['db_pass'] ?? '';
    $dbname = $_POST['db_name'] ?? '';

    try {
        // Connect to MySQL server (no DB yet)
        $pdo = new PDO("mysql:host=$host", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        // Create DB if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        // Connect to the new DB
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // Create tables if not exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS flashcards (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            question TEXT NOT NULL,
            answer TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Save config
        $config = "<?php\nreturn [\n    'host' => '" . addslashes($host) . "',\n    'user' => '" . addslashes($user) . "',\n    'pass' => '" . addslashes($pass) . "',\n    'name' => '" . addslashes($dbname) . "'\n];\n";
        file_put_contents(DB_CONFIG_FILE, $config);
        $success = true;
    } catch (PDOException $e) {
        $error = 'Setup failed: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Local Flashcards Setup</title>
    <style>
        body { font-family: sans-serif; background: #f8f8f8; }
        .setup-container { max-width: 400px; margin: 40px auto; background: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
        input[type=text], input[type=password] { width: 100%; padding: 0.5em; margin-bottom: 1em; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 0.7em 2em; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: #b00; margin-bottom: 1em; }
        .success { color: #080; margin-bottom: 1em; }
    </style>
</head>
<body>
<div class="setup-container">
    <h2>Local Flashcards Setup</h2>
    <p>Congratulations! You've successfully installed XAMPP and started all the necessary servers. Now, we need to set up the MySQL database for the application. While you could do this manually by visiting <a href="http://localhost/phpmyadmin/" target="_blank">localhost/phpmyadmin</a>, we've made it simple for you to do it here.</p>
    <?php if ($success): ?>
        <div class="success">Setup complete! <a href="index.php">Go to app</a></div>
    <?php else: ?>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="post">
            <label>MySQL Host:<br><input type="text" name="db_host" value="localhost" required placeholder="e.g., 127.0.0.1 or localhost"></label><br>
            <label>MySQL Username:<br><input type="text" name="db_user" required></label><br>
            <label>MySQL Password:<br><input type="password" name="db_pass"></label><br>
            <label>Database Name:<br><input type="text" name="db_name" value="local_flashcards" required></label><br>
            <button type="submit">Set Up</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
