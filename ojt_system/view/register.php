<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OJT Portal</title>
    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="card" style="max-width: 450px; width: 100%;">
            <h2><i class='bx bx-user-plus' style="vertical-align: middle; margin-right: 8px;"></i>Registration</h2>
            
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert error"><i class="bx bx-error-circle"></i> ' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <form action="../controller/action_register.php" method="POST" class="deployment-form">
                <label><i class='bx bx-id-card'></i> First Name</label>
                <input type="text" name="first_name" required placeholder="John">
                
                
                <label><i class='bx bx-id-card'></i> Last Name</label>
                <input type="text" name="last_name" required placeholder="Doe">
                
                <label><i class='bx bx-envelope'></i> School Email Address</label>
                <input type="email" name="email" placeholder="student@university.edu" autocomplete="off" required>
                
                <label><i class='bx bx-key'></i> Password</label>
                <input type="password" name="password" required placeholder="••••••••">
                
                <button type="submit" name="register_btn" class="btn-clock-in" style="margin-top: 10px;">
                    Submit Registration <i class='bx bx-check-circle'></i>
                </button>
            </form>
            
            <p style="text-align: center; margin-top: 25px; font-weight: 500; font-size: 0.95rem; color: var(--text-muted);">
                Already have an account? <br>
                <a href="login.php" style="color: var(--primary); text-decoration: none; display: inline-block; margin-top: 8px; font-weight: 600;"><i class='bx bx-left-arrow-alt'></i> Back to Login</a>
            </p>
        </div>
    </div>
</body>
</html>