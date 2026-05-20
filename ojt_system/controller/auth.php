<?php
session_start();
require_once '../model/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']) === false) {
        die("Invalid CSRF token. Please try again.");
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../view/login.php?error=empty_fields");
        exit();
    }

    $sql = "SELECT user_id, first_name, password_hash, role, status FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Database prepare error: " . $conn->error);
        die("An unexpected error occurred.");
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password_hash'])) {
            if ($user['status'] == 'Pending') {
                header("Location: ../view/login.php?error=pending");
                exit();
            }
            
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'Admin') {
                header("Location: ../view/admin_dashboard.php");
            } elseif ($user['role'] == 'Student') {
                header("Location: ../view/student_dashboard.php");
            } else {
                header("Location: ../view/supervisor_dashboard.php");
            }
            exit();
        } else {
            sleep(1);
            header("Location: ../view/login.php?error=wrong_password");
            exit();
        }
    } else {
        sleep(1);
        header("Location: ../view/login.php?error=not_found");
        exit();
    }
    $stmt->close();
}
?>