<?php
function getActiveDeployment($conn, $student_id) {
    $sql = "SELECT deployment_id, required_hours FROM Deployments WHERE student_id = ? AND status = 'Active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getCurrentActiveLog($conn, $deployment_id) {
    $sql = "SELECT log_id, time_in, time_out FROM Attendance_Logs WHERE deployment_id = ? AND log_date = CURDATE() AND time_out IS NULL ORDER BY log_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deployment_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function timeIn($conn, $deployment_id) {
    $sql = "INSERT INTO Attendance_Logs (deployment_id, log_date, time_in, approval_status) VALUES (?, CURDATE(), CURTIME(), 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deployment_id);
    return $stmt->execute();
}

function timeOut($conn, $log_id, $narrative) {
    $sql = "UPDATE Attendance_Logs SET time_out = CURTIME(), narrative = ? WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $narrative, $log_id);
    return $stmt->execute();
}

function getApprovedHours($conn, $deployment_id) {
    $sql = "SELECT SUM(TIMESTAMPDIFF(HOUR, time_in, time_out)) AS total_hours FROM Attendance_Logs WHERE deployment_id = ? AND approval_status = 'Approved'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deployment_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total_hours'] ?? 0;
}

function getAttendanceHistory($conn, $deployment_id) {
    $sql = "SELECT log_date, time_in, time_out, task_category, approval_status, narrative FROM Attendance_Logs WHERE deployment_id = ? ORDER BY log_date DESC, time_in DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deployment_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fileLeave($conn, $deployment_id, $date, $reason) {
    $category = "LEAVE: " . $reason;
    $status = "Pending";
    $sql = "INSERT INTO Attendance_Logs (deployment_id, log_date, task_category, approval_status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $deployment_id, $date, $category, $status);
    return $stmt->execute();
}

function getStudentProfile($conn, $user_id) {
    $sql = "SELECT u.first_name, u.last_name, u.email, u.status, u.phone, u.address AS home_address, u.resume_file, c.company_name, c.address AS company_address, sup.first_name AS sup_fname, sup.last_name AS sup_lname, d.required_hours 
            FROM Users u 
            LEFT JOIN Deployments d ON u.user_id = d.student_id AND d.status = 'Active' 
            LEFT JOIN Companies c ON d.company_id = c.company_id 
            LEFT JOIN Users sup ON d.supervisor_id = sup.user_id 
            WHERE u.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getStudentProfileByDeployment($conn, $deployment_id) {
    $sql = "SELECT u.first_name, u.last_name, u.email, u.phone, u.address AS home_address, u.resume_file, c.company_name, c.address AS company_address, sup.first_name AS sup_fname, sup.last_name AS sup_lname, d.required_hours 
            FROM Deployments d 
            JOIN Users u ON d.student_id = u.user_id 
            JOIN Companies c ON d.company_id = c.company_id 
            JOIN Users sup ON d.supervisor_id = sup.user_id 
            WHERE d.deployment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deployment_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateStudentProfile($conn, $user_id, $first_name, $last_name, $phone, $address, $resume_file) {
    if ($resume_file) {
        $sql = "UPDATE Users SET first_name=?, last_name=?, phone=?, address=?, resume_file=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $first_name, $last_name, $phone, $address, $resume_file, $user_id);
    } else {
        $sql = "UPDATE Users SET first_name=?, last_name=?, phone=?, address=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $first_name, $last_name, $phone, $address, $user_id);
    }
    return $stmt->execute();
}

function getStudentDocuments($conn, $student_id) {
    $sql = "SELECT * FROM Documents WHERE student_id = ? ORDER BY uploaded_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>