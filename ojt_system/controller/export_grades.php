<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Supervisor'])) {
    header("Location: ../view/login.php");
    exit();
}

require_once '../model/database.php';


if (!isset($_GET['id'])) {
    die("Error: No student selected for export.");
}

$deployment_id = $_GET['id'];


$query = "
    SELECT 
        CONCAT(u.first_name, ' ', u.last_name) AS student_name,
        c.company_name,
        CONCAT(sup.first_name, ' ', sup.last_name) AS supervisor_name,
        e.punctuality,
        e.skills,
        e.attitude,
        e.comments
    FROM evaluations e
    JOIN deployments d ON e.deployment_id = d.deployment_id
    JOIN users u ON d.student_id = u.user_id
    JOIN companies c ON d.company_id = c.company_id
    JOIN users sup ON d.supervisor_id = sup.user_id
    WHERE d.deployment_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $deployment_id);

if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if (!$data) {
        die("Error: No evaluation found for this student.");
    }

    
    $clean_name = str_replace(' ', '_', $data['student_name']);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Evaluation_' . $clean_name . '.csv');

    $output = fopen('php://output', 'w');
    
    
    $overall = round(($data['punctuality'] + $data['skills'] + $data['attitude']) / 3, 2);
    
    fputcsv($output, array('Student Name', 'Company', 'Supervisor', 'Punctuality', 'Technical Skills', 'Attitude', 'Overall Average', 'Comments'));
    fputcsv($output, array($data['student_name'], $data['company_name'], $data['supervisor_name'], $data['punctuality'], $data['skills'], $data['attitude'], $overall . '%', $data['comments']));
    
    fclose($output);
}
exit();
?>