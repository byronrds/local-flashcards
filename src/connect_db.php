<?php

$config_file = __DIR__ . '/db_config.php';
$current_script = basename($_SERVER['SCRIPT_NAME']);
if (!file_exists($config_file)) {
   if ($current_script !== 'setup.php') {
      header('Location: setup.php');
      exit();
   }
}
$config = require $config_file;

$host = $config['host'] ?? '';
$dbname = $config['name'] ?? '';
$username = $config['user'] ?? '';
$password = $config['pass'] ?? '';

if ((!$host || !$dbname || !$username) && $current_script !== 'setup.php') {
   header('Location: setup.php');
   exit();
}

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
   $db = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
   $error_message = $e->getMessage();
   echo "<p>An error occurred while connecting to the database: $error_message </p>";
   exit();
} catch (Exception $e) {
   $error_message = $e->getMessage();
   echo "<p>Error message: $error_message </p>";
   exit();
}
?>