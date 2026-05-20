<?php
require_once 'model/database.php';

$email = 'admin@admin.com';
$password = 'admin123';
$first_name = 'System';
$last_name = 'Admin';
$role = 'Admin';
$status = 'Active';

$sql_check = "SELECT user_id FROM Users WHERE role = 'Admin'";
$result = $conn->query($sql_check);

if ($result->num_rows === 0) {
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    
    $sql_insert = "INSERT INTO Users (email, password_hash, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ssssss", $email, $hashed_pw, $first_name, $last_name, $role, $status);
    
    if ($stmt->execute()) {
        echo "<div style='background-color:#1e4620; color:#81c784; padding:30px; text-align:center; font-family:Arial; border-radius:8px; width:400px; margin:50px auto; border:1px solid #2e7d32;'>";
        echo "<h2 style='margin-top:0;'>Master Admin Created!</h2>";
        echo "<p><b>Email:</b> admin@admin.com</p>";
        echo "<p><b>Password:</b> admin123</p>";
        echo "<br><p style='color:#cf6679; font-size:0.9rem;'><b>CRITICAL:</b> Delete this setup_admin.php file from your folder immediately after logging in to prevent hackers from recreating it.</p>";
        echo "</div>";
    }
} else {
    echo "<div style='background-color:#3e2723; color:#ffb74d; padding:30px; text-align:center; font-family:Arial; border-radius:8px; width:400px; margin:50px auto; border:1px solid #d84315;'>";
    echo "<h2 style='margin-top:0;'>Admin Already Exists</h2>";
    echo "<p>The database already has an Admin account.</p>";
    echo "</div>";
}
?>