<?php
session_start();
require_once '../model/database.php';
require_once '../model/evaluations.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['submit_evaluation'])) {
        $deployment_id = $_POST['deployment_id'];
        $punct = $_POST['punctuality'];
        $skills = $_POST['skills'];
        $att = $_POST['attitude'];
        $comments = $_POST['comments'];

        if (saveEvaluation($conn, $deployment_id, $punct, $skills, $att, $comments)) {
            $_SESSION['message'] = "<div class='alert success'>Final Evaluation saved successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert warning'>Evaluation already exists for this student.</div>";
        }
        header("Location: ../view/supervisor_dashboard.php");
        exit();
    }
}
header("Location: ../index.php");
exit();
?>