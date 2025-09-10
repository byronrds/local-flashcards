<?php
    require "../config/database_connection.php";
    require "../includes/user_functions.php";

    // Form processing is now handled in the template below

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up | FlashMaster</title>
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
    <style>
        body { background: #2c2e31; font-family: system-ui, -apple-system, sans-serif; color: #e2e2e2; }
        .container { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; }
        .card { background: #323437; border: 1px solid #4a4d52; border-radius: 8px; padding: 3rem 2.5rem; width: 100%; max-width: 480px; }
        .header { text-align: center; margin-bottom: 2.5rem; }
        .header h1 { font-size: 1.75rem; font-weight: 700; color: #FFD700; margin-bottom: 0.5rem; }
        .header p { color: #b5b5b5; font-size: 0.9rem; }
        .back-btn { display: inline-block; margin-bottom: 1.5rem; background: #3a3d42; color: #e2e2e2; border: 1px solid #4a4d52; border-radius: 4px; padding: 0.5rem 1rem; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: background 0.2s; }
        .back-btn:hover { background: #424549; }
        .form-group { margin-bottom: 1.5rem; }
        .label { display: block; color: #e2e2e2; font-weight: 600; margin-bottom: 0.5rem; }
        .input { width: 100%; padding: 0.75rem; border: 1px solid #4a4d52; border-radius: 4px; font-size: 1rem; background: #3a3d42; color: #e2e2e2; transition: border-color 0.2s; }
        .input:focus { outline: none; border-color: #FFD700; }
        .btn { width: 100%; background: #FFD700; color: #000000; font-weight: 600; padding: 0.75rem; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #FFC107; }
        .footer { text-align: center; margin-top: 2rem; color: #b5b5b5; font-size: 0.9rem; }
        .footer a { color: #FFD700; text-decoration: underline; }
        .error { background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1rem; margin-bottom: 1.5rem; color: #e2e2e2; }
        .success { background: #3a3d42; border: 1px solid #4a4d52; border-radius: 4px; padding: 1rem; margin-bottom: 1.5rem; color: #e2e2e2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <a href="login.php" class="back-btn">← Back to Login</a>
            
            <div class="header">
                <h1>Create Account</h1>
                <p>Join FlashMaster and start organizing your study materials</p>
            </div>
            
            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <?php if (!empty($_POST['user_id']) && !empty($_POST['password']) && !empty($_POST['confirmPassword'])): ?>
                    <?php if ($_POST['password'] !== $_POST['confirmPassword']): ?>
                        <div class="error">Passwords do not match. Please try again.</div>
                    <?php else: ?>
                        <?php 
                        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        if (createUser($_POST['user_id'], $hashedPassword)): ?>
                            <div class="success">Account created successfully! <a href="login.php">Click here to sign in</a></div>
                        <?php else: ?>
                            <div class="error">Username already exists. Please choose a different username.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="error">Please fill in all fields.</div>
                <?php endif; ?>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="form-group">
                    <label class="label">Username</label>
                    <input type="text" name="user_id" class="input" value="<?php echo htmlspecialchars($_POST['user_id'] ?? ''); ?>" required />
                </div>
                <div class="form-group">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input" required />
                </div>
                <div class="form-group">
                    <label class="label">Confirm Password</label>
                    <input type="password" name="confirmPassword" class="input" required />
                </div>
                <button type="submit" class="btn">Create Account</button>
            </form>
            
            <div class="footer">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </div>
    </div>
</body>

</html>

