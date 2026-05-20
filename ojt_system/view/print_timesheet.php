<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/attendance.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$deployment_id = $_GET['id'];
$profile = getStudentProfileByDeployment($conn, $deployment_id);
$history = getAttendanceHistory($conn, $deployment_id);
$approved_hours = getApprovedHours($conn, $deployment_id);
$required_hours = $profile['required_hours'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Timesheet - <?php echo htmlspecialchars($profile['last_name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #000; background: #fff; padding: 40px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 14px; }
        .details-table { width: 100%; margin-bottom: 30px; }
        .details-table td { padding: 5px; font-size: 14px; }
        .details-table td strong { display: inline-block; width: 150px; }
        .log-table { width: 100%; border-collapse: collapse; margin-bottom: 50px; }
        .log-table th, .log-table td { border: 1px solid #000; padding: 10px; font-size: 13px; text-align: left; }
        .log-table th { background-color: #f0f0f0; }
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
        .sig-box { width: 40%; text-align: center; }
        .sig-line { border-top: 1px solid #000; padding-top: 10px; margin-top: 50px; font-weight: bold; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #0056b3; color: white; border: none; cursor: pointer;">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer;">Close</button>
    </div>

    <div class="header">
        <h1>Official OJT Timesheet Report</h1>
        <p>Generated on <?php echo date('F d, Y'); ?></p>
    </div>

    <table class="details-table">
        <tr>
            <td><strong>Student Name:</strong> <?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></td>
            <td><strong>Company Name:</strong> <?php echo htmlspecialchars($profile['company_name']); ?></td>
        </tr>
        <tr>
            <td><strong>Email Address:</strong> <?php echo htmlspecialchars($profile['email']); ?></td>
            <td><strong>Direct Supervisor:</strong> <?php echo htmlspecialchars($profile['sup_fname'] . ' ' . $profile['sup_lname']); ?></td>
        </tr>
        <tr>
            <td><strong>Target Hours:</strong> <?php echo $required_hours; ?> Hrs</td>
            <td><strong>Total Approved:</strong> <?php echo $approved_hours; ?> Hrs</td>
        </tr>
    </table>

    <table class="log-table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 15%;">Time In</th>
                <th style="width: 15%;">Time Out</th>
                <th style="width: 40%;">Narrative Report / Task</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $row): ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($row['log_date'])); ?></td>
                <td><?php echo htmlspecialchars($row['time_in'] ?? '--'); ?></td>
                <td><?php echo htmlspecialchars($row['time_out'] ?? '--'); ?></td>
                <td>
                    <?php 
                    if (strpos($row['task_category'], 'LEAVE') === 0) {
                        echo "<b>ON LEAVE:</b> " . htmlspecialchars(str_replace('LEAVE: ', '', $row['task_category']));
                    } else {
                        echo htmlspecialchars($row['narrative'] ?? 'N/A');
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($row['approval_status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line">
                <?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?><br>
                <span style="font-weight: normal; font-size: 12px;">Student Signature</span>
            </div>
        </div>
        <div class="sig-box">
            <div class="sig-line">
                <?php echo htmlspecialchars($profile['sup_fname'] . ' ' . $profile['sup_lname']); ?><br>
                <span style="font-weight: normal; font-size: 12px;">Supervisor Signature</span>
            </div>
        </div>
    </div>

</body>
</html>