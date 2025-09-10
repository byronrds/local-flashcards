<?php
    require "../config/database_connection.php";
    require "../includes/user_functions.php";

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

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup</title>
    <link rel="stylesheet" href="../assets/css/output.css">
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="p-6 rounded-lg shadow-xl w-96">
        <h1 class="text-xl font-semibold text-center text-gray-700">Sign Up</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="mt-4">
            <div class="mb-3">
                <label class="block text-gray-600 font-medium">Username</label>
                <input type="text" name="user_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
            </div>
            <div class="mb-3">
                <label class="block text-gray-600 font-medium">Password</label>
                <input type="password" name="password" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
            </div>
            <div class="mb-3">
                <label class="block text-gray-600 font-medium">Confirm Password</label>
                <input type="password" name="confirmPassword" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Submit
            </button>
        </form>
    </div>
</body>

</html>

