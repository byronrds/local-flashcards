<?php
    require "connect_db.php";
    require "user_db.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! empty($_POST['user_id']) && ! empty($_POST['password']) && ! empty($_POST['confirmPassword'])) {
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

    require "header.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup</title>
    <link rel="stylesheet" href="./output.css">
</head>
<body>
    <div class='flex items-center justify-center min-h-screen'>
        <div class='border border-indigo-600 p-4 rounded-lg'>
        <p class='text-md text-blue-500 text-center mb-4'>Sign Up</p>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <p>Username:</p>
            <input type="text" name="user_id" class="border border-indigo-600 rounded-lg" autofocus required /> <br/>
            <p>Password:</p>
            <input type="password" name="password" class="border border-indigo-600 rounded-lg" required /> <br/>
            <p>Confirm Password:</p>
            <input type="password" name="confirmPassword" class="border border-indigo-600 rounded-lg" required /> <br/>
            <button type="submit">Submit</button>
        </form>
</div>
    </div>
</body>
</html>

