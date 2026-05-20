<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../view/login.php");
    exit();
}

require_once '../model/database.php';


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=OJT_Student_Masterlist_' . date('Y-m-d') . '.csv');


$output = fopen('php://output', 'w');


fputcsv($output, array('User ID', 'First Name', 'Last Name', 'Email', 'Account Status'));


$query = "SELECT user_id, first_name, last_name, email, status FROM users WHERE role = 'Student' ORDER BY last_name ASC";
$stmt = $conn->prepare($query);

if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['user_id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['status']
        ));
    }
}

fclose($output);
exit();
?>