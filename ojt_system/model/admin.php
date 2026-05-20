<?php
function getPendingUsers($conn) {
    $sql = "SELECT user_id, first_name, last_name, email, role FROM Users WHERE status = 'Pending'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function approveUser($conn, $user_id) {
    $sql = "UPDATE Users SET status = 'Active' WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function getUndeployedStudents($conn) {
    $sql = "SELECT u.user_id, u.first_name, u.last_name FROM Users u LEFT JOIN Deployments d ON u.user_id = d.student_id WHERE u.role = 'Student' AND u.status = 'Active' AND d.deployment_id IS NULL";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getCompanies($conn) {
    $sql = "SELECT company_id, company_name FROM Companies";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getSupervisors($conn) {
    $sql = "SELECT user_id, first_name, last_name FROM Users WHERE role = 'Supervisor' AND status = 'Active'";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createDeployment($conn, $student_id, $company_id, $supervisor_id, $hours) {
    $sql = "INSERT INTO Deployments (student_id, company_id, supervisor_id, required_hours, status) VALUES (?, ?, ?, ?, 'Active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $student_id, $company_id, $supervisor_id, $hours);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function getActiveDeployments($conn) {
    $sql = "SELECT d.deployment_id, s.first_name AS student_name, c.company_name, sup.first_name AS supervisor_name, d.status 
            FROM Deployments d 
            JOIN Users s ON d.student_id = s.user_id 
            JOIN Companies c ON d.company_id = c.company_id 
            JOIN Users sup ON d.supervisor_id = sup.user_id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addSupervisor($conn, $first_name, $last_name, $username, $password) {
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Users (email, password_hash, first_name, last_name, role, status) VALUES (?, ?, ?, ?, 'Supervisor', 'Active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $hashed_pw, $first_name, $last_name);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function addCompany($conn, $company_name, $address, $contact_number) {
    $sql = "INSERT INTO Companies (company_name, address, contact_number) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $company_name, $address, $contact_number);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function getAllUsersDirectory($conn) {
    $sql = "SELECT user_id, first_name, last_name, email, role FROM Users WHERE role != 'Admin' ORDER BY role, first_name";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function resetUserPassword($conn, $user_id, $new_password) {
    $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE Users SET password_hash = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashed_pw, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function getAllStudents($conn) {
    $sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.status, d.deployment_id 
            FROM Users u 
            LEFT JOIN Deployments d ON u.user_id = d.student_id 
            WHERE u.role = 'Student' 
            ORDER BY u.last_name ASC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function addStudent($conn, $first_name, $last_name, $email, $password) {
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Users (email, password_hash, first_name, last_name, role, status) VALUES (?, ?, ?, ?, 'Student', 'Active')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email, $hashed_pw, $first_name, $last_name);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}



function getStudentById($conn, $user_id) {
    $sql = "SELECT * FROM Users WHERE user_id = ? AND role = 'Student'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

function getStudentDeployment($conn, $student_id) {
    $sql = "SELECT * FROM Deployments WHERE student_id = ? AND status = 'Active' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}

function updateStudentAdmin($conn, $user_id, $first_name, $last_name, $email, $status) {
    $sql = "UPDATE Users SET first_name = ?, last_name = ?, email = ?, status = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $status, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function deleteUser($conn, $user_id) {
    
    $conn->query("DELETE FROM Attendance_Logs WHERE deployment_id IN (SELECT deployment_id FROM Deployments WHERE student_id = $user_id)");
    $conn->query("DELETE FROM Deployments WHERE student_id = $user_id");
    
    $sql = "DELETE FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function updateDeploymentAdmin($conn, $deployment_id, $company_id, $supervisor_id, $hours) {
    $sql = "UPDATE Deployments SET company_id = ?, supervisor_id = ?, required_hours = ? WHERE deployment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $company_id, $supervisor_id, $hours, $deployment_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function removeDeployment($conn, $deployment_id) {
    $conn->query("DELETE FROM Attendance_Logs WHERE deployment_id = $deployment_id");
    $sql = "DELETE FROM Deployments WHERE deployment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $deployment_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
    
}
function getDashboardAnalytics($conn) {
    $analytics = [];
    
    $res = $conn->query("SELECT status, COUNT(*) as count FROM Users WHERE role = 'Student' GROUP BY status");
    $analytics['status_dist'] = $res->fetch_all(MYSQLI_ASSOC);

    $res = $conn->query("SELECT c.company_name, COUNT(d.deployment_id) as count FROM Companies c LEFT JOIN Deployments d ON c.company_id = d.company_id AND d.status='Active' GROUP BY c.company_id");
    $analytics['company_dist'] = $res->fetch_all(MYSQLI_ASSOC);
    return $analytics;
}

function getPendingDocuments($conn) {
    $sql = "SELECT doc.*, u.first_name, u.last_name FROM Documents doc JOIN Users u ON doc.student_id = u.user_id WHERE doc.status = 'Pending'";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

function updateDocumentStatus($conn, $doc_id, $status) {
    $sql = "UPDATE Documents SET status = ? WHERE doc_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $doc_id);
    return $stmt->execute();
}
?>
