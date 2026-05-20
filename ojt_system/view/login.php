<?php
session_start();


if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'Admin') {
        header("Location: admin_dashboard.php");
    } elseif ($_SESSION['role'] == 'Student') {
        header("Location: student_dashboard.php");
    } else {
        header("Location: supervisor_dashboard.php");
    }
    exit();
}


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - OJT Portal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="card" style="max-width: 400px; width: 100%;">
            <h2>Sign In</h2>
            
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                echo '<div class="alert error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_GET['error'])) {
                if ($_GET['error'] == 'pending') {
                    echo '<div class="alert warning">Account is pending approval.</div>';
                } elseif ($_GET['error'] == 'wrong_password' || $_GET['error'] == 'not_found') {
                    echo '<div class="alert error">Invalid credentials.</div>';
                } elseif ($_GET['error'] == 'empty_fields') {
                    echo '<div class="alert error">Please fill in all fields.</div>';
                }
            }
            ?>

            <form action="../controller/auth.php" method="POST" class="deployment-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <label>Email or Username</label>
<input type="text" name="email" required>
                
                <label>Password</label>
                <input type="password" name="password" required>
                
                <button type="submit" name="login_btn" class="btn-clock-in">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 25px; font-weight: bold; font-size: 0.95rem;">
                Don't have an account? <br>
                <a href="register.php" style="color: #0056b3; text-decoration: none; display: inline-block; margin-top: 8px;">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>