<?php 
require("connect_db.php");
require("user_db.php");
session_start();

// redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = getUserByUserId($user_id);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <link rel="stylesheet" href="./output.css">
</head>

<body>
    <?php require("header.php"); ?>

    <div class="p-6">
        <p class="text-md">Hey <?php echo htmlspecialchars($user['user_id']); ?>. Let's study!</p>
        <p>Test</p>

        <button onClick="window.location='create_flashcards.php';" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" >Create Flashcard Set</button>
        
    </div>
</body>
</html>
