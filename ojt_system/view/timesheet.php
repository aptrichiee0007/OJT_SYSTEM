<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Supervisor'])) {
    header("Location: login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/attendance.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$deployment_id = $_GET['id'];
$history = getAttendanceHistory($conn, $deployment_id);
$details = getDeploymentDetails($conn, $deployment_id);
$back_link = $_SESSION['role'] === 'Admin' ? 'admin_dashboard.php' : 'supervisor_dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timesheet</title>
    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper" style="align-items: flex-start; padding-top: 40px;">
        <div class="card" style="max-width: 800px; width: 100%;">
            <a href="<?php echo $back_link; ?>" style="color: var(--primary); text-decoration: none; font-weight: bold;"><i class='bx bx-arrow-back'></i> Back to Dashboard</a>
            
            <h2 style="margin-top: 20px; color: var(--text);">
                <i class='bx bx-time-five'></i> Timesheet: <?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?>
            </h2>
            <p style="color: var(--text-muted); margin-bottom: 20px;"><i class='bx bx-building'></i> Deployed at: <?php echo htmlspecialchars($details['company_name']); ?></p>

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
                            <th>Details</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['log_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['time_in'] ?? '--:--:--'); ?></td>
                            <td><?php echo htmlspecialchars($row['time_out'] ?? '--:--:--'); ?></td>
                            <td><?php echo htmlspecialchars($row['task_category'] ?? 'Daily Log'); ?></td>
                            <td>
                                <?php if ($row['approval_status'] == 'Approved'): ?>
                                    <span style="color: var(--success); font-weight: bold;"><i class='bx bx-check'></i> Approved</span>
                                <?php elseif ($row['approval_status'] == 'Rejected'): ?>
                                    <span style="color: var(--danger); font-weight: bold;"><i class='bx bx-x'></i> Rejected</span>
                                <?php else: ?>
                                    <span style="color: var(--warning); font-weight: bold;"><i class='bx bx-time'></i> Pending</span>
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