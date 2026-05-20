<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/attendance.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $resume_file = null;

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_extension = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));
        
        if ($file_extension !== "pdf") {
            $_SESSION['message'] = "<div class='alert error'>Upload failed: Only PDF files are allowed.</div>";
            header("Location: ../view/student_dashboard.php");
            exit();
        }

        $new_filename = "resume_user" . $user_id . "_" . time() . ".pdf";
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
            $resume_file = $new_filename;
        } else {
            $_SESSION['message'] = "<div class='alert error'>Critical error: Failed to save file to server.</div>";
            header("Location: ../view/student_dashboard.php");
            exit();
        }
    }

    if (updateStudentProfile($conn, $user_id, $first_name, $last_name, $phone, $address, $resume_file)) {
        $_SESSION['first_name'] = $first_name; 
        $_SESSION['message'] = "<div class='alert success'>Profile and Resume updated successfully!</div>";
    } else {
        $_SESSION['message'] = "<div class='alert error'>Database error: Failed to update profile.</div>";
    }
    
    header("Location: ../view/student_dashboard.php");
    exit();
}
header("Location: ../index.php");
exit();
?>