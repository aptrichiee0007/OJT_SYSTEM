<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/attendance.php';

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? 'Student';
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$profile = getStudentProfile($conn, $user_id);
$deployment = getActiveDeployment($conn, $user_id);
$has_deployment = $deployment !== null;

$active_log = null;
$approved_hours = 0;
$required_hours = 0;
$progress_percent = 0;
$history = [];
$my_docs = getStudentDocuments($conn, $user_id);

if ($has_deployment) {
    $deployment_id = $deployment['deployment_id'];
    $required_hours = $deployment['required_hours'];
    $active_log = getCurrentActiveLog($conn, $deployment_id);
    $approved_hours = getApprovedHours($conn, $deployment_id);
    $history = getAttendanceHistory($conn, $deployment_id);
    if ($required_hours > 0) {
        $progress_percent = min(100, ($approved_hours / $required_hours) * 100);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - OJT System</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
        </i> OJT Student Portal
        </div>
        <div class="nav-user">
            <span>Welcome, <b><?php echo htmlspecialchars($first_name); ?></b></span>
            <a href="../controller/logout.php" class="btn-logout"><i class='bx bx-log-out'></i> Sign Out</a>
        </div>
    </nav>

    <div class="dashboard-layout">
<aside class="sidebar">
    <button class="tab-btn active" onclick="openTab(event, 'tab-clock')">
         Time Clock
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-progress')">
         My Progress
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-docs')">
        📁 My Requirements
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-leave')">
         File Leave
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-profile')">
         My Profile
    </button>
</aside>

        <main class="content-area">
            <?php echo $message; ?>

            <div id="tab-leave" class="tab-content">
    <div class="card center-card" style="max-width: 600px; margin: 0 auto; border: 1px solid #0056b3;">
        
        <h3 style="display: flex; justify-content: center; align-items: center; gap: 8px; width: 100%;">
            📅 REQUEST ABSENCE / LEAVE
        </h3>
        
        <?php if (!$has_deployment): ?>
            <div class="empty-state" style="padding: 30px; text-align: center;">
                <p>You must be deployed to file a leave.</p>
            </div>
        <?php else: ?>
            <form action="../controller/action_attendance.php" method="POST" class="deployment-form" style="padding-top: 10px;">
                <input type="hidden" name="deployment_id" value="<?php echo $deployment_id; ?>">
                
                <label style="font-weight: bold; font-size: 0.9rem; color: #333; display: block; margin-bottom: 5px; text-align: center;">Date of Absence</label>
                <input type="date" name="leave_date" value="<?php echo date('Y-m-d'); ?>" required style="cursor: pointer; padding: 12px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 15px; width: 100%; font-family: inherit;">
                
                <label style="font-weight: bold; font-size: 0.9rem; color: #333; display: block; margin-bottom: 5px; text-align: center;">Reason for Leave</label>
                <input type="text" name="reason" placeholder="e.g., Medical checkup, University activity..." required style="padding: 12px; border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 25px; width: 100%; font-family: inherit;">
                
                <button type="submit" name="file_leave" class="btn-clock-in action" style="width: 100%; padding: 14px; font-weight: bold; border-radius: 50px; background: #fbbf24; color: #111827; border: 1px solid #d97706; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px; display: flex; justify-content: center; align-items: center; gap: 6px;">
                    <i class='bx bx-paper-plane'></i> SUBMIT LEAVE REQUEST
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
            <div id="tab-clock" class="tab-content" style="display: block;">
                <div class="card center-card" style="max-width: 800px;">
                    
                    <?php if (!$has_deployment): ?>
                        <div class="alert warning">You have not been deployed to a company yet.</div>
                    <?php else: ?>
                        
                        <div style="display: flex; flex-wrap: wrap; justify-content: space-evenly; align-items: center; margin-bottom: 40px; padding-bottom: 30px; border-bottom: 2px dashed #e9ecef;">
                            <div style="text-align: center; padding: 15px;">
                                <p style="color: #6c757d; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Current Time</p>
                                <div class="clock-display" id="liveClock" style="margin-bottom: 0;">00:00:00</div>
                            </div>

                            <div style="text-align: center; padding: 15px;">
                                <p style="color: #6c757d; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">Overall Progress</p>
                                <div class="circular-progress" style="--progress: <?php echo $progress_percent; ?>%; margin: 0 auto;">
                                    <div class="progress-value">
                                        <?php echo $approved_hours; ?>
                                        <span>/ <?php echo $required_hours; ?> Hrs</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="action-section" style="max-width: 500px; margin: 0 auto;">
                            <?php if (!$active_log): ?>
                                <form action="../controller/action_attendance.php" method="POST">
                                    <input type="hidden" name="deployment_id" value="<?php echo $deployment_id; ?>">
                                    <button type="submit" name="time_in" class="btn-clock-in" style="font-size: 1.2rem; padding: 18px;"><i class='bx bx-play-circle'></i> START SHIFT (TIME IN)</button>
                                </form>
                            <?php else: ?>
                                <div class="log-status" style="margin-bottom: 20px; font-size: 1.1rem; text-align: center; color: var(--success); font-weight: 600;">
                                    <i class='bx bx-radio-circle-marked bx-burst'></i> Shift Active: Started at <?php echo date("h:i A", strtotime($active_log['time_in'])); ?>
                                </div>
                                <form action="../controller/action_attendance.php" method="POST" style="text-align: center; margin-top: 15px;">
                                    <input type="hidden" name="log_id" value="<?php echo $active_log['log_id']; ?>">
                                    <input type="hidden" name="narrative" value="No narrative required.">
                                    <button type="submit" name="time_out" class="btn-clock-out" style="font-size: 1.2rem; padding: 18px; width: 100%;"><i class='bx bx-stop-circle'></i> END SHIFT (TIME OUT)</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

<div id="tab-progress" class="tab-content">
                <div class="card" style="max-width: 1000px;">
                    
                    <h3 style="position: relative; display: flex; justify-content: center; align-items: center;">
                        
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i class='bx bx-history' style="font-size: 1.4rem;"></i> MY TIMESHEET HISTORY
                        </span>
                        
                        <?php if ($has_deployment): ?>
                            <a href="print_timesheet.php?id=<?php echo $deployment_id; ?>" target="_blank" style="position: absolute; right: 20px; background: #dc2626; color: white; padding: 6px 16px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 0.8rem; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.15); text-transform: none; letter-spacing: normal; line-height: 1.2;">
                                <i class='bx bx-printer' style="font-size: 1.4rem;"></i> 
                                <span style="text-align: left;">Print to<br>PDF</span>
                            </a>
                        <?php endif; ?>
                        
                    </h3>
                    
                    <?php if (empty($history)): ?>
                        <div class="empty-state">
                            <i class='bx bx-ghost'></i>
                            <p>No attendance records found.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time Log</th>
                                    <th>Daily Narrative / Task</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $row): ?>
                                <tr>
                                    <td style="font-weight: bold; width: 150px;"><?php echo date('M d, Y', strtotime($row['log_date'])); ?></td>
                                    <td style="width: 150px;">
                                        <span style="color: #28a745; font-weight:bold; display:block;">IN: <?php echo htmlspecialchars($row['time_in'] ?? '--:--:--'); ?></span>
                                        <span style="color: #dc3545; font-weight:bold; display:block;">OUT: <?php echo htmlspecialchars($row['time_out'] ?? '--:--:--'); ?></span>
                                    </td>
                                    <td>
                                        <?php if (strpos($row['task_category'], 'LEAVE') === 0): ?>
                                            <span style="color: #f59e0b; font-weight:bold;">On Leave</span>
                                            <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: #6c757d; font-style: italic;"><?php echo htmlspecialchars(str_replace('LEAVE: ', '', $row['task_category'])); ?></p>
                                        <?php elseif (!empty($row['narrative']) && $row['narrative'] !== 'No narrative required.'): ?>
                                            <p style="margin: 0; font-size: 0.95rem; color: #333;"><?php echo htmlspecialchars($row['narrative']); ?></p>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-style: italic;">Shift completed.</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['approval_status'] == 'Approved'): ?>
                                            <span class="status-badge success">Approved</span>
                                        <?php elseif ($row['approval_status'] == 'Rejected'): ?>
                                            <span class="status-badge error" style="background:#dc3545; color:white;">Rejected</span>
                                        <?php else: ?>
                                            <span class="status-badge warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-docs" class="tab-content">
                <div class="admin-grid">
                    <div class="admin-column" style="flex: 1;">
                        <div class="card admin-card">
                            <h3 style="display: flex; justify-content: center; align-items: center; gap: 8px;">
    <i class='bx bx-cloud-upload'></i> Submit Requirement
</h3>
                            <form action="../controller/action_documents.php" method="POST" enctype="multipart/form-data" class="deployment-form">
                                <label>Document Type</label>
                                <select name="document_type" required>
                                    <option value="">Choose...</option>
                                    <option value="Memorandum of Agreement (MOA)">Memorandum of Agreement (MOA)</option>
                                    <option value="Waiver Form">Waiver Form</option>
                                    <option value="Medical Certificate">Medical Certificate</option>
                                    <option value="Endorsement Letter">Endorsement Letter</option>
                                </select>
                                
                                <label>Attach File (PDF or Image)</label>
                                <input type="file" name="document_file" accept=".pdf, .jpg, .jpeg, .png" required style="padding: 10px; background-color: #f8f9fa; border: 2px dashed #0056b3; margin-bottom: 20px;">
                                
                                <button type="submit" name="upload_document" class="btn-clock-in action">Upload Document</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="admin-column" style="flex: 2;">
                        <div class="card admin-card">
                            <h3 style="display: flex; justify-content: center; align-items: center; gap: 8px;">
    <i class='bx bx-folder-open'></i> My Uploaded Documents
</h3>
                            <?php if(empty($my_docs)): ?>
                                <div class="empty-state">
                                    <i class='bx bx-file-blank'></i>
                                    <p>You have not uploaded any requirements yet.</p>
                                </div>
                            <?php else: ?>
                                <table class="data-table">
                                    <thead><tr><th>Document</th><th>Date Uploaded</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <?php foreach($my_docs as $doc): ?>
                                        <tr>
                                            <td style="font-weight: bold;"><?php echo htmlspecialchars($doc['document_type']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($doc['uploaded_at'])); ?></td>
                                            <td>
                                                <?php if($doc['status'] == 'Approved'): ?>
                                                    <span class="status-badge success">Approved</span>
                                                <?php elseif($doc['status'] == 'Rejected'): ?>
                                                    <span class="status-badge error" style="background:#dc3545; color:white;">Rejected</span>
                                                <?php else: ?>
                                                    <span class="status-badge warning">Pending Review</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

<div id="tab-profile" class="tab-content">
    <div class="admin-grid" style="display: flex; gap: 30px; align-items: flex-start;">
        
        <div class="admin-column" style="flex: 1; display: flex; flex-direction: column; gap: 20px;">
            
            <div class="card admin-card" style="padding: 30px; border: 1px solid #0056b3;">
                <h3><i class='bx bx-id-card'></i> Personal Information</h3>
                <div class="info-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 20px;">
                    <div class="info-card full-width" style="grid-column: span 2;">
                        <label>Full Name</label>
                        <p><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></p>
                    </div>
                    <div class="info-card">
                        <label>Email Address</label>
                        <p><?php echo htmlspecialchars($profile['email']); ?></p>
                    </div>
                    <div class="info-card">
                        <label>Contact Number</label>
                        <p><?php echo htmlspecialchars($profile['phone'] ?? 'Not provided'); ?></p>
                    </div>
                    <div class="info-card full-width" style="grid-column: span 2;">
                        <label>Home Address</label>
                        <p><?php echo htmlspecialchars($profile['home_address'] ?? 'Not provided'); ?></p>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #28a745;">
                        <label>Uploaded Resume</label>
                        <?php if (!empty($profile['resume_file'])): ?>
                            <a href="../uploads/<?php echo htmlspecialchars($profile['resume_file']); ?>" target="_blank" class="btn-small success" style="margin-top: 8px; text-decoration: none;"><i class='bx bx-file'></i> View Resume</a>
                        <?php else: ?>
                            <p style="color: #dc3545; font-size: 0.95rem; margin-top: 8px;">No resume.</p>
                        <?php endif; ?>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #8b5cf6;">
                        <label>OJT Requirements</label>
                        <?php if (empty($my_docs)): ?>
                            <p style="font-size: 0.85rem; margin-top: 8px;">No docs.</p>
                        <?php else: ?>
                            <div style="display: flex; gap: 5px; flex-wrap: wrap; margin-top: 8px;">
                                <?php foreach($my_docs as $doc): ?>
                                    <span class="status-badge <?php echo ($doc['status'] == 'Approved') ? 'success' : 'warning'; ?>" style="font-size: 0.7rem;"><?php echo htmlspecialchars($doc['document_type']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card admin-card" style="padding: 30px; border: 1px solid #0056b3;">
                <h3><i class='bx bx-buildings'></i> Deployment Details</h3>
                <?php if (!$has_deployment): ?>
                    <div class="empty-state" style="margin-top: 15px;"><p>Not currently assigned.</p></div>
                <?php else: ?>
                    <div class="info-grid" style="grid-template-columns: 1fr; margin-top: 20px;">
                        <div class="info-card">
                            <label>Company</label>
                            <p><?php echo htmlspecialchars($profile['company_name']); ?></p>
                        </div>
                        <div class="info-card">
                            <label>Supervisor</label>
                            <p><?php echo htmlspecialchars($profile['sup_fname'] . ' ' . $profile['sup_lname']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-column" style="flex: 1;">
            <div class="card admin-card" style="padding: 30px; border: 1px solid #0056b3;">
                <h3><i class='bx bx-edit-alt'></i> Update Profile & Resume</h3>
                <form action="../controller/action_profile.php" method="POST" enctype="multipart/form-data" class="deployment-form" style="margin-top: 20px;">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($profile['first_name']); ?>" required>
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($profile['last_name']); ?>" required>
                    <label>Contact Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                    <label>Home Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($profile['home_address'] ?? ''); ?>">
                    <label>Upload New Resume (PDF Only)</label>
                    <input type="file" name="resume" accept="application/pdf" style="padding: 10px; background-color: #f8f9fa; border: 2px dashed #0056b3; margin-bottom: 20px;">
                    
                    <button type="submit" name="update_profile" class="btn-clock-in" style="background: #007bff; border-radius: 50px;">
                        <i class='bx bx-save'></i> SAVE CHANGES
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="tab-profile" class="tab-content">
                <div class="admin-grid">
                    <div class="admin-column">
                        <div class="card admin-card">
                            <h3><i class='bx bx-id-card'></i> Personal Information</h3>
                            
                            <div class="info-grid">
                                
                                <div class="info-card full-width">
                                    <label>Full Name</label>
                                    <p><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></p>
                                </div>
                                
                                <div class="info-card">
                                    <label>Email Address</label>
                                    <p><?php echo htmlspecialchars($profile['email']); ?></p>
                                </div>
                                
                                <div class="info-card">
                                    <label>Contact Number</label>
                                    <p><?php echo htmlspecialchars($profile['phone'] ?? 'Not provided'); ?></p>
                                </div>
                                
                                <div class="info-card full-width">
                                    <label>Home Address</label>
                                    <p><?php echo htmlspecialchars($profile['home_address'] ?? 'Not provided'); ?></p>
                                </div>
                                
                                <div class="info-card" style="border-left-color: #28a745; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <label>Uploaded Resume</label>
                                    <?php if (!empty($profile['resume_file'])): ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($profile['resume_file']); ?>" target="_blank" class="btn-small success" style="margin-top: 8px; text-decoration: none;"><i class='bx bx-file'></i> View My Resume</a>
                                    <?php else: ?>
                                        <p style="color: #dc3545; font-size: 0.95rem; margin-top: 8px;">No resume uploaded.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="info-card" style="border-left-color: #8b5cf6; text-align: center; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <label>OJT Requirements</label>
                                    <?php if (empty($my_docs)): ?>
                                        <p style="color: #6c757d; font-size: 0.85rem; margin-top: 8px; font-weight: normal;">No documents uploaded yet.</p>
                                    <?php else: ?>
                                        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 5px; margin-top: 8px;">
                                            <?php foreach($my_docs as $doc): ?>
                                                <a href="../uploads/<?php echo $doc['file_path']; ?>" target="_blank" class="status-badge <?php echo ($doc['status'] == 'Approved') ? 'success' : (($doc['status'] == 'Rejected') ? 'error' : 'warning'); ?>" style="text-decoration: none; border: 1px solid currentColor; font-size: 0.75rem; padding: 4px 8px;">
                                                    <i class='bx bx-file'></i> <?php echo htmlspecialchars($doc['document_type']); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                            
                            <h3 style="margin-top: 30px;"><i class='bx bx-buildings'></i> Deployment Details</h3>
                            <?php if (!$has_deployment): ?>
                                <div class="empty-state">
                                    <i class='bx bx-map-pin'></i>
                                    <p>Not currently assigned to a company.</p>
                                </div>
                            <?php else: ?>
                                <div class="info-grid">
                                    <div class="info-card full-width">
                                        <label>Company</label>
                                        <p><?php echo htmlspecialchars($profile['company_name']); ?></p>
                                    </div>
                                    <div class="info-card full-width">
                                        <label>Supervisor</label>
                                        <p><?php echo htmlspecialchars($profile['sup_fname'] . ' ' . $profile['sup_lname']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="admin-column">
                        <div class="card admin-card">
                            <h3><i class='bx bx-edit-alt'></i> Update Profile & Resume</h3>
                            <form action="../controller/action_profile.php" method="POST" enctype="multipart/form-data" class="deployment-form">
                                <label>First Name</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($profile['first_name']); ?>" required>
                                
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($profile['last_name']); ?>" required>
                                
                                <label>Contact Number</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                                
                                <label>Home Address</label>
                                <input type="text" name="address" value="<?php echo htmlspecialchars($profile['home_address'] ?? ''); ?>">
                                
                                <label>Upload New Resume (PDF Only)</label>
                                <input type="file" name="resume" accept="application/pdf" style="padding: 10px; background-color: #f8f9fa; border: 2px dashed #0056b3; margin-bottom: 20px;">
                                
                                <button type="submit" name="update_profile" class="btn-clock-in"><i class='bx bx-save'></i> Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/tabs.js"></script>
    <script src="../assets/js/clock.js"></script>
</body>
</html>