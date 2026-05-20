<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Supervisor'])) {
    header("Location: login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/attendance.php';
require_once '../model/evaluations.php'; 

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$deployment_id = $_GET['id'];
$profile = getStudentProfileByDeployment($conn, $deployment_id);
if (!$profile) {
    $back_link = $_SESSION['role'] === 'Admin' ? 'admin_dashboard.php' : 'supervisor_dashboard.php';
    header('Location: ' . $back_link);
    exit();
}
$history = getAttendanceHistory($conn, $deployment_id);
$approved_hours = getApprovedHours($conn, $deployment_id);
$required_hours = $profile['required_hours'];
$progress_percent = $required_hours > 0 ? min(100, ($approved_hours / $required_hours) * 100) : 0;
$evaluation = getEvaluation($conn, $deployment_id);
$back_link = $_SESSION['role'] === 'Admin' ? 'admin_dashboard.php' : 'supervisor_dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student File</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="display: block; padding: 40px; background-color: #fdf6e3;">
    
    <div style="max-width: 1000px; margin: 0 auto;">
        
        <a href="<?php echo $back_link; ?>" style="color: #0056b3; text-decoration: none; font-weight: bold; font-size: 1.1rem; display: inline-block; margin-bottom: 20px;">
            <i class='bx bx-arrow-back'></i> Back to Dashboard
        </a>
        
        <div class="card" style="margin-bottom: 30px; text-align: left;">
            
<div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;">
    <h2 style="margin: 0; color: #0056b3; font-size: 1.6rem;">
        <i class='bx bx-user-circle'></i> Student File: <?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?>
    </h2>

    <div style="display: flex; gap: 10px; align-items: center;">
        <?php if (!empty($profile['resume_file'])): ?>
            <a href="../uploads/<?php echo htmlspecialchars($profile['resume_file']); ?>" target="_blank" class="btn-small" style="background: #333; color: white; border-color: #222; text-decoration: none;">
                <i class='bx bxs-file-pdf'></i> View Resume
            </a>
        <?php else: ?>
            <span style="color: #6c757d; font-style: italic; font-size: 0.9rem; margin-right: 10px;"><i class='bx bx-info-circle'></i> No resume</span>
        <?php endif; ?>

        <?php if ($evaluation): ?>
            <a href="../controller/export_grades.php?id=<?php echo $deployment_id; ?>" class="btn-small" style="background: #8b5cf6; color: white; border-color: #7c3aed; text-decoration: none;">
                <i class='bx bx-export'></i> Export Final Grade
            </a>
        <?php endif; ?>
    </div>
</div>

            <div class="info-grid">
                
                <div class="info-card">
                    <label>Email / Username</label>
                    <p><?php echo htmlspecialchars($profile['email']); ?></p>
                </div>
                
                <div class="info-card">
                    <label>Contact Number</label>
                    <p><?php echo htmlspecialchars($profile['phone'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="info-card full-width">
                    <label>Home Address</label>
                    <p><?php echo htmlspecialchars($profile['home_address'] ?? 'N/A'); ?></p>
                </div>
                
                <div class="info-card">
                    <label>Company</label>
                    <p><?php echo htmlspecialchars($profile['company_name']); ?></p>
                </div>
                
                <div class="info-card">
                    <label>Assigned Supervisor</label>
                    <p><?php echo htmlspecialchars($profile['sup_fname'] . ' ' . $profile['sup_lname']); ?></p>
                </div>
                
                <div class="info-card full-width" style="border-left-color: #28a745; margin-top: 10px;">
                    <label>Hours Completed</label>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 10px;">
                        <p style="font-size: 1.6rem; color: #28a745;">
                            <?php echo $approved_hours; ?> <span style="font-size: 1rem; color: #64748b;">/ <?php echo $required_hours; ?> Hrs</span>
                        </p>
                        <span style="font-weight: bold; color: #28a745; font-size: 1.2rem;">
                            <?php echo round($progress_percent); ?>%
                        </span>
                    </div>
                    <div class="progress-bar-bg" style="height: 12px; background-color: #e2e8f0; border: none;">
                        <div class="progress-bar-fill" style="width: <?php echo $progress_percent; ?>%; background: linear-gradient(to right, #28a745, #34d399);"></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card" style="text-align: center;">
            <h3 style="margin: -30px -30px 20px -30px; padding: 15px 30px; background: #ffe066; color: #333; border-bottom: 3px solid #0056b3; font-size: 1.3rem; text-transform: uppercase; border-radius: 5px 5px 0 0; display: flex; align-items: center; position: relative;">
    
    <div style="width: 80px;"></div>

    <span style="flex-grow: 1; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
        <i class='bx bx-time-five'></i> Timesheet & Activity History
    </span>

    <a href="print_timesheet.php?id=<?php echo $deployment_id; ?>" target="_blank" 
       style="width: 80px; background: #dc2626; color: white; padding: 4px 10px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
        <i class='bx bx-printer' style="font-size: 1rem;"></i> 
        <span>PDF</span>
    </a>
</h3>
            
            <?php if (empty($history)): ?>
                <div class="empty-state">
                    <i class='bx bx-ghost'></i>
                    <p>No attendance records found for this student.</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Shift Status</th>
                            <th>Approval</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $row): ?>
                        <tr>
                            <td style="font-weight: bold;"><?php echo date('l, M d, Y', strtotime($row['log_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['time_in'] ?? '--:--:--'); ?></td>
                            <td><?php echo htmlspecialchars($row['time_out'] ?? '--:--:--'); ?></td>
                            <td>
                                <?php if (strpos($row['task_category'], 'LEAVE') === 0): ?>
                                    <span style="color: #ffc107; font-weight: bold;"><i class='bx bx-calendar-x'></i> On Leave</span>
                                <?php elseif ($row['time_in'] && !$row['time_out']): ?>
                                    <span style="color: #0056b3; font-weight: bold; animation: pulse 2s infinite;"><i class='bx bx-loader-alt bx-spin'></i> In Progress</span>
                                <?php else: ?>
                                    <span style="color: #28a745; font-weight: bold;"><i class='bx bx-check-circle'></i> Shift Completed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['approval_status'] == 'Approved'): ?>
                                    <span class="status-badge success"><i class='bx bx-check'></i> Approved</span>
                                <?php elseif ($row['approval_status'] == 'Rejected'): ?>
                                    <span class="status-badge error" style="background:#dc3545; color:white;"><i class='bx bx-x'></i> Rejected</span>
                                <?php else: ?>
                                    <span class="status-badge warning" style="color: #333;"><i class='bx bx-time'></i> Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
    </div>
</body>
</html>