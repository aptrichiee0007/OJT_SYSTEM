<?php
session_start();
require_once '../model/database.php';
require_once '../model/attendance.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['time_in'])) {
        $deployment_id = $_POST['deployment_id'];
        if (timeIn($conn, $deployment_id)) {
            $_SESSION['message'] = "<div class='alert success'>Shift started successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to start shift. Error recorded.</div>";
        }
    } 
    elseif (isset($_POST['time_out'])) {
        $log_id = $_POST['log_id'];
        $narrative = $_POST['narrative'] ?? 'No narrative provided.';
        if (timeOut($conn, $log_id, $narrative)) {
            $_SESSION['message'] = "<div class='alert success'>Shift ended. Daily narrative saved!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to end shift.</div>";
        }
    }
    elseif (isset($_POST['file_leave'])) {
        $deployment_id = $_POST['deployment_id'];
        $leave_date = $_POST['leave_date'];
        $reason = $_POST['reason'];
        
        if (fileLeave($conn, $deployment_id, $leave_date, $reason)) {
            $_SESSION['message'] = "<div class='alert success'>Leave request submitted to your supervisor.</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to submit leave request.</div>";
        }
    }

    header("Location: ../view/student_dashboard.php");
    exit();
}
?>