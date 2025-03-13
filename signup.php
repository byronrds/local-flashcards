<?php
require("connect_db.php");
require("user_db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['user_id']) && !empty($_POST['password']) && !empty($_POST['confirmPassword'])) {
        if ($_POST['password'] == $_POST['confirmPassword']) {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            if (createUser($_POST['user_id'], $hashedPassword)) {
                header("Location: login.php");
                exit();
            } else {
                echo "<h2>User ID already exists. Please choose a different one.</h2>";
            }
        } else {
            echo "<h2>Passwords do not match</h2>";
        }
    }
}

require("header.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">  
    <title>Signup</title>       
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post"> 
            user_id: <input type="text" name="user_id" class="form-control" autofocus required /> <br/>
            Password: <input type="password" name="password" class="form-control" required /> <br/>
            Confirm Password: <input type="password" name="confirmPassword" class="form-control" required /> <br/>
            <input type="submit" value="Sign Up" class="btn btn-light" />   
        </form>
    </div>
</body>
</html>

