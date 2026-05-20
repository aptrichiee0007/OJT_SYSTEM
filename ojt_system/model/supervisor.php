<?php
function getAssignedStudents($conn, $supervisor_id) {
    $sql = "SELECT 
                u.user_id, u.first_name, u.last_name, 
                c.company_name, 
                d.required_hours, 
                d.status,
                d.deployment_id,
                (SELECT MAX(log_date) FROM Attendance_Logs WHERE deployment_id = d.deployment_id AND task_category NOT LIKE 'LEAVE%') as last_active_date,
                al.time_in as today_in,
                al.time_out as today_out,
                al.task_category as today_category
            FROM Deployments d 
            JOIN Users u ON d.student_id = u.user_id 
            JOIN Companies c ON d.company_id = c.company_id 
            LEFT JOIN Attendance_Logs al ON d.deployment_id = al.deployment_id AND al.log_date = CURDATE()
            WHERE d.supervisor_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($data as $key => $student) {
        $status_class = 'neutral';
        $status_label = 'Inactive';
        
        if (!empty($student['today_category']) && strpos($student['today_category'], 'LEAVE') === 0) {
            $status_class = 'warning';
            $status_label = 'On Leave';
        } elseif (!empty($student['today_in']) && empty($student['today_out'])) {
            $status_class = 'success';
            $status_label = 'Clocked In';
        } elseif (!empty($student['last_active_date'])) {
            $last_active = new DateTime($student['last_active_date']);
            $now = new DateTime();
            $diff = $now->diff($last_active)->days;
            if ($diff >= 14) {
                $status_class = 'error'; 
                $status_label = 'AWOL (14+ Days)';
            }
        }
        
        $data[$key]['status_class'] = $status_class;
        $data[$key]['status_label'] = $status_label;
    }
    
    return $data;
}

function getPendingTimeLogs($conn, $supervisor_id) {
    $sql = "SELECT a.log_id, a.log_date, a.time_in, a.time_out, a.narrative, u.first_name, u.last_name 
            FROM Attendance_Logs a
            JOIN Deployments d ON a.deployment_id = d.deployment_id
            JOIN Users u ON d.student_id = u.user_id
            WHERE d.supervisor_id = ? 
            AND a.approval_status = 'Pending' 
            AND a.time_out IS NOT NULL
            ORDER BY a.log_date ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getPendingLeaves($conn, $supervisor_id) {
    $sql = "SELECT a.log_id, a.log_date, a.task_category, u.first_name, u.last_name 
            FROM Attendance_Logs a 
            JOIN Deployments d ON a.deployment_id = d.deployment_id 
            JOIN Users u ON d.student_id = u.user_id 
            WHERE d.supervisor_id = ? AND a.approval_status = 'Pending' AND a.task_category LIKE 'LEAVE%'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function getSupervisorStats($conn, $supervisor_id) {
    $stats = ['total_students' => 0, 'pending_actions' => 0, 'active_today' => 0];
    
    
    $sql = "SELECT COUNT(*) as count FROM Deployments WHERE supervisor_id = ? AND status = 'Active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    $stats['total_students'] = $stmt->get_result()->fetch_assoc()['count'];
    
    
    $sql = "SELECT COUNT(*) as count FROM Attendance_Logs a JOIN Deployments d ON a.deployment_id = d.deployment_id WHERE d.supervisor_id = ? AND a.approval_status = 'Pending' AND (a.time_out IS NOT NULL OR a.task_category LIKE 'LEAVE%')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    $stats['pending_actions'] = $stmt->get_result()->fetch_assoc()['count'];
    
    
    $sql = "SELECT COUNT(DISTINCT a.deployment_id) as count FROM Attendance_Logs a JOIN Deployments d ON a.deployment_id = d.deployment_id WHERE d.supervisor_id = ? AND a.log_date = CURDATE() AND a.task_category NOT LIKE 'LEAVE%'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    $stats['active_today'] = $stmt->get_result()->fetch_assoc()['count'];
    
    $stmt->close();
    return $stats;
}

function updateLogStatus($conn, $log_id, $status) {
    $sql = "UPDATE Attendance_Logs SET approval_status = ? WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $log_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function resetTimeOut($conn, $log_id) {
    $sql = "UPDATE Attendance_Logs SET time_out = NULL WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $log_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}
?>