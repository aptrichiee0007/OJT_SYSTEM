<?php
session_start();
require_once '../model/database.php';
require_once '../model/admin.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    if (isset($_POST['upload_document'])) {
        $student_id = $_SESSION['user_id'];
        $doc_type = $_POST['document_type']; 
        
        if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
            $ext = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), ['pdf', 'jpg', 'jpeg', 'png'])) {
                $file_name = preg_replace("/[^a-zA-Z0-9]+/", "", $doc_type) . "_" . $student_id . "_" . time() . "." . $ext;
                
                if(move_uploaded_file($_FILES['document_file']['tmp_name'], "../uploads/" . $file_name)) {
                    $sql = "INSERT INTO Documents (student_id, document_type, file_path) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iss", $student_id, $doc_type, $file_name);
                    $stmt->execute();
                    $_SESSION['message'] = "<div class='alert success'>Document uploaded and is pending review.</div>";
                }
            } else {
                $_SESSION['message'] = "<div class='alert error'>Invalid file. Please upload PDF or Images only.</div>";
            }
        }
        header("Location: ../view/student_dashboard.php");
        exit();
    }

    
    if (isset($_POST['approve_document'])) {
        updateDocumentStatus($conn, $_POST['doc_id'], 'Approved');
        $_SESSION['message'] = "<div class='alert success'>Document officially approved!</div>";
        header("Location: ../view/admin_dashboard.php");
        exit();
    } 
    elseif (isset($_POST['reject_document'])) {
        updateDocumentStatus($conn, $_POST['doc_id'], 'Rejected');
        $_SESSION['message'] = "<div class='alert error'>Document rejected. Student must re-upload.</div>";
        header("Location: ../view/admin_dashboard.php");
        exit();
    }
}
?>