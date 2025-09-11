<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require "../config/database_connection.php";
    require "../includes/user_functions.php";

    session_start();
?>

<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['user_id']) && !empty($_POST['password'])) {
            $user = getUserByUserId($_POST['user_id']);
            if ($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                header("Location: index.php");
                exit();
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
    <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
    <title>Login | FlashMaster</title>
    <style>
        body { background: #2c2e31; font-family: system-ui, -apple-system, sans-serif; color: #e2e2e2; }
        .container { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; }
        .card { background: #323437; border: 1px solid #4a4d52; border-radius: 8px; padding: 3rem 2.5rem; width: 100%; max-width: 480px; }
        .brand { text-align: center; margin-bottom: 2.5rem; }
        .brand h1 { font-size: 2rem; font-weight: 700; color: #FFD700; margin-bottom: 0.5rem; }
        .brand p { color: #b5b5b5; font-size: 1rem; line-height: 1.5; }
        .form-group { margin-bottom: 1.5rem; }
        .label { display: block; color: #e2e2e2; font-weight: 600; margin-bottom: 0.5rem; }
        .input { width: 100%; padding: 0.75rem; border: 1px solid #4a4d52; border-radius: 4px; font-size: 1rem; background: #3a3d42; color: #e2e2e2; transition: border-color 0.2s; }
        .input:focus { outline: none; border-color: #FFD700; }
        .btn { width: 100%; background: #FFD700; color: #000000; font-weight: 600; padding: 0.75rem; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #FFC107; }
        .footer { text-align: center; margin-top: 2rem; color: #b5b5b5; font-size: 0.9rem; }
        .footer a { color: #FFD700; text-decoration: underline; }
        .error { background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1rem; margin-bottom: 1.5rem; color: #e2e2e2; }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="brand">
                <h1>FlashMaster</h1>
                <p>Your personal study companion. All your flashcards stored securely on your own device using MySQL. No cloud dependencies, complete data ownership, and lightning-fast performance.</p>
            </div>
            
                    <?php if (isset($_GET['account_deleted'])): ?>
                        <div style="background: #22c55e; border: 1px solid #16a34a; border-radius: 4px; padding: 1rem; margin-bottom: 1.5rem; color: #ffffff;">
                            ✅ Your account has been successfully deleted. All your data has been permanently removed.
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && (!empty($_POST['user_id']) || !empty($_POST['password']))): ?>
                        <div class="error">
                            <?php
                            if (empty($_POST['user_id']) || empty($_POST['password'])) {
                                echo "Please fill in all fields.";
                            } else {
                                $user = getUserByUserId($_POST['user_id']);
                                if (!$user) {
                                    echo "User not found. Please check your username or create an account.";
                                } else if (!password_verify($_POST['password'], $user['password'])) {
                                    echo "Incorrect password. Please try again.";
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="form-group">
                    <label class="label">Username</label>
                    <input type="text" name="user_id" class="input" required />
                </div>
                <div class="form-group">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input" required />
                </div>
                <button type="submit" class="btn">Sign In</button>
            </form>
            
            <div class="footer">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </div>
    </div>
</body>
</html>

