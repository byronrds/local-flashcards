<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require("connect_db.php");
require("user_db.php"); 

session_start();
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['user_id']) && !empty($_POST['password'])) {
        $user = getUserByUserId($_POST['user_id']);

        if (!$user) {
            echo "<h2>User not found in database</h2>";
        } else {
            if (password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                header("Location: index.php");
                exit();  
            } else {
                echo "<h2>Incorrect password</h2>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">  
    <title>Login</title>       
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post"> 
            Username: <input type="text" name="user_id" class="form-control" autofocus required /> <br/>
            Password: <input type="password" name="password" class="form-control" required /> <br/>
            <input type="submit" value="Sign in" class="btn btn-light" />   
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up</a></p> 
    </div>
</body>
</html>
