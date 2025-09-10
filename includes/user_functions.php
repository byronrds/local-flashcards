<?php
require("../config/database_connection.php");

function createUser($user_id, $hashedPassword) {
    global $db;
  
    if (!$db) {
        die("<p>Database connection error in createUser()</p>");
    }

    try {
        $query = "INSERT INTO user (user_id, password) VALUES (:user_id, :password)";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->bindValue(':password', $hashedPassword);
        $statement->execute();
        $statement->closeCursor();
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Code 23000 means "Duplicate entry" in MySQL
            echo "<p>Error: User ID already exists. Choose a different one.</p>";
        } else {
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
        return false;
    }
}

function getUserByUserId($user_id) {
    global $db;

    if (!$db) {
        die("<p>Database connection error in getUserByUserId()</p>");
    }

    try {
        $query = "SELECT * FROM user WHERE user_id = :user_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return $user ?: null; 
    } catch (PDOException $e) {
        echo "<p>Error fetching user: " . $e->getMessage() . "</p>";
        return null;
    }
}
?>

