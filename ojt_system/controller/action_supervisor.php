<?php
session_start();
require_once '../model/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Supervisor') {
    header("Location: ../view/login.php");
    exit();
}

if (isset($_POST['log_id']) && isset($_POST['action'])) {
    $log_id = $_POST['log_id'];
    $action = $_POST['action'];

    if ($action === 'Approve' || $action === 'Reject') {
        $status = ($action === 'Approve') ? 'Approved' : 'Rejected';
        
        $sql = "UPDATE Attendance_Logs SET approval_status = ? WHERE log_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $log_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "<div class='alert success'>Action successfully processed.</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Error processing request. Please try again.</div>";
        }
    }
    
    header("Location: ../view/supervisor_dashboard.php");
    exit();
}

header("Location: ../view/supervisor_dashboard.php");
exit();
?>