<?php
function saveEvaluation($conn, $deployment_id, $punctuality, $skills, $attitude, $comments) {
    
    $check = "SELECT eval_id FROM Evaluations WHERE deployment_id = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("i", $deployment_id);
    $stmt->execute();
    if($stmt->get_result()->num_rows > 0) return false;

    $sql = "INSERT INTO Evaluations (deployment_id, punctuality, skills, attitude, comments) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", $deployment_id, $punctuality, $skills, $attitude, $comments);
    return $stmt->execute();
}

function getEvaluation($conn, $deployment_id) {
    $sql = "SELECT * FROM Evaluations WHERE deployment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deployment_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>