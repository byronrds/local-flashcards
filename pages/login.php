<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require "../config/database_connection.php";
    require "../includes/user_functions.php";

    session_start();
?>

<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! empty($_POST['user_id']) && ! empty($_POST['password'])) {
            $user = getUserByUserId($_POST['user_id']);

            if (! $user) {
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
    <link rel="stylesheet" href="../assets/css/output.css">
    <title>Login</title>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="p-6 rounded-lg shadow-xl w-96">
        <h1 class="text-xl font-semibold text-center text-gray-700">Login</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="mt-4">
            <div class="mb-3">
                <label class="block text-gray-600 font-medium">Username</label>
                <input type="text" name="user_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
            </div>
            <div class="mb-3">
                <label class="block text-gray-600 font-medium">Password</label>
                <input type="password" name="password" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
            </div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Sign In
            </button>
        </form>
        <p class="text-center mt-4 text-sm">Don't have an account? <a href="signup.php" class="text-blue-500">Sign up</a></p>
    </div>
</body>
</html>

