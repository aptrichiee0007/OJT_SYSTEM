<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Supervisor') {
    header("Location: login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/supervisor.php';
require_once '../model/evaluations.php';

$supervisor_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$stats = getSupervisorStats($conn, $supervisor_id);
$assigned_students = getAssignedStudents($conn, $supervisor_id);
$pending_logs = getPendingTimeLogs($conn, $supervisor_id);
$pending_leaves = getPendingLeaves($conn, $supervisor_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard</title>
    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand"></i> OJT Supervisor Portal</div>
        <div class="nav-user">
            <span>Welcome, <b><?php echo htmlspecialchars($first_name); ?></b></span>
            <a href="../controller/logout.php" class="btn-logout"><i class='bx bx-log-out'></i> Sign Out</a>
        </div>
    </nav>

    <div class="dashboard-layout">
<aside class="sidebar">
    <button class="tab-btn active" onclick="openTab(event, 'tab-overview')">
       💼 Overview
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-students')">
        👥 My Students
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-approvals')">
         Time Approvals
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-leaves')">
        📅 Leave Requests
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-evals')">
        ⭐ Final Evaluations
    </button>
</aside>

        <main class="content-area">
            <?php echo $message; ?>

            <div id="tab-overview" class="tab-content" style="display: block;">
                <h2 style="color: #0056b3; margin-top: 0; margin-bottom: 24px; text-transform: uppercase;">
                    <i class='bx bx-bar-chart-alt-2'></i> Dashboard Overview
                </h2>
                
                <div class="dashboard-stats-grid">
                    <div class="stat-card">
                        <i class='bx bx-group icon' style="color: #007bff;"></i>
                        <h3><?php echo $stats['total_students']; ?></h3>
                        <p>Total Assigned</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class='bx bx-time-five icon' style="color: #ffc107;"></i>
                        <h3><?php echo $stats['pending_actions']; ?></h3>
                        <p>Pending Actions</p>
                    </div>
                    
                    <div class="stat-card">
                        <i class='bx bx-check-circle icon' style="color: #28a745;"></i>
                        <h3><?php echo $stats['active_today']; ?></h3>
                        <p>Active Today</p>
                    </div>
                </div>

                <div class="card admin-card" style="width: 100%; max-width: 100%; margin-top: 30px;">
                    <h3><i class='bx bx-bell'></i> Needs Immediate Attention</h3>
                    
                    <?php if(empty($pending_logs) && empty($pending_leaves)): ?>
                        <div class="empty-state">
                            <i class='bx bx-party' style="font-size: 3rem; color: #6c757d; margin-bottom: 10px;"></i>
                            <p>You're all caught up! No pending approvals or leave requests.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Student</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 0; foreach ($pending_logs as $log): if($count >= 3) break; ?>
                                <tr>
                                    <td><span class="status-badge" style="background:#ffc107; color:#333;">Time Log</span></td>
                                    <td style="font-weight: bold;"><?php echo htmlspecialchars($log['last_name'] . ', ' . $log['first_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($log['log_date'])); ?></td>
                                    <td><button class="btn-small action" onclick="document.querySelector('[onclick*=\'tab-approvals\']').click()">Review</button></td>
                                </tr>
                                <?php $count++; endforeach; ?>

                                <?php $count = 0; foreach ($pending_leaves as $leave): if($count >= 2) break; ?>
                                <tr>
                                    <td><span class="status-badge" style="background:#0ea5e9; color:white;">Leave Req</span></td>
                                    <td style="font-weight: bold;"><?php echo htmlspecialchars($leave['last_name'] . ', ' . $leave['first_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($leave['log_date'])); ?></td>
                                    <td><button class="btn-small action" onclick="document.querySelector('[onclick*=\'tab-leaves\']').click()">Review</button></td>
                                </tr>
                                <?php $count++; endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-students" class="tab-content">
                <div class="card admin-card">
                    <h3><i class='bx bx-group'></i> My Assigned Students</h3>
                    <?php if (empty($assigned_students)): ?>
                        <div class="empty-state">
                            <i class='bx bx-user-x'></i>
                            <p>No students assigned to you yet.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Target Hours</th>
                                    <th>Live Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assigned_students as $student): ?>
                                <tr>
                                    <td style="font-weight: bold;"><a href="view_student.php?id=<?php echo $student['deployment_id']; ?>" style="color: #3b82f6; text-decoration: none;"><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></a></td>
                                    <td><?php echo htmlspecialchars($student['required_hours']); ?> Hrs</td>
                                    <td><span class="status-badge <?php echo $student['status_class']; ?>"><?php echo $student['status_label']; ?></span></td>
                                    <td><a href="view_student.php?id=<?php echo $student['deployment_id']; ?>" class="btn-small action" style="text-decoration:none;"><i class='bx bx-folder-open'></i> View Timesheet</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-approvals" class="tab-content">
                <div class="card admin-card">
                    <h3><i class='bx bx-time-five'></i> Pending Time Logs</h3>
                    <?php if(empty($pending_logs)): ?>
                        <div class="empty-state">
                            <i class='bx bx-check-double'></i>
                            <p>No pending time logs require approval at this time.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead><tr><th>Student</th><th>Date</th><th>Time In</th><th>Time Out</th><th>Narrative</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($pending_logs as $log): ?>
                                <tr>
                                    <td style="font-weight: bold;"><?php echo htmlspecialchars($log['last_name'] . ', ' . $log['first_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($log['log_date'])); ?></td>
                                    <td style="color: #10b981; font-weight:bold;"><?php echo date('h:i A', strtotime($log['time_in'])); ?></td>
                                    <td style="color: #ef4444; font-weight:bold;"><?php echo date('h:i A', strtotime($log['time_out'])); ?></td>
                                    <td style="font-size: 0.85rem; max-width: 200px;"><?php echo htmlspecialchars($log['narrative']); ?></td>
                                    <td>
                                        <form action="../controller/action_supervisor.php" method="POST" style="display:flex; gap:5px;">
                                            <input type="hidden" name="log_id" value="<?php echo $log['log_id']; ?>">
                                            <button type="submit" name="action" value="Approve" class="btn-small success"><i class='bx bx-check'></i> Approve</button>
                                            <button type="submit" name="action" value="Reject" class="btn-small" style="background:#ef4444; color:white;"><i class='bx bx-x'></i> Reject</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-leaves" class="tab-content">
                <div class="card admin-card">
                    <h3><i class='bx bx-calendar-minus'></i> Student Leave Requests</h3>
                    <?php if(empty($pending_leaves)): ?>
                        <div class="empty-state">
                            <i class='bx bx-calendar-check'></i>
                            <p>No pending leave requests at this time.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead><tr><th>Student</th><th>Date Requested</th><th>Reason</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($pending_leaves as $leave): ?>
                                <tr>
                                    <td style="font-weight: bold;"><?php echo htmlspecialchars($leave['last_name'] . ', ' . $leave['first_name']); ?></td>
                                    <td style="color: #0ea5e9; font-weight:bold;"><?php echo date('l, M d, Y', strtotime($leave['log_date'])); ?></td>
                                    <td style="font-style: italic;"><?php echo htmlspecialchars(str_replace('LEAVE: ', '', $leave['task_category'])); ?></td>
                                    <td>
                                        <form action="../controller/action_supervisor.php" method="POST" style="display:flex; gap:5px;">
                                            <input type="hidden" name="log_id" value="<?php echo $leave['log_id']; ?>">
                                            <button type="submit" name="action" value="Approve" class="btn-small success"><i class='bx bx-check'></i> Approve</button>
                                            <button type="submit" name="action" value="Reject" class="btn-small" style="background:#ef4444; color:white;"><i class='bx bx-x'></i> Deny</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

<div id="tab-evals" class="tab-content">
                <div class="card admin-card" style="max-width: 900px;">
                    <h3><i class='bx bx-star'></i> Submit Final OJT Evaluation</h3>
                    <p style="color: #64748b; margin-top: -10px; margin-bottom: 20px;">Score the student out of 100 in each category.</p>
                    
                    <form action="../controller/action_evaluations.php" method="POST" class="deployment-form">
                        <label>Select Student</label>
                        <select name="deployment_id" required>
                            <option value="">Choose...</option>
                            <?php foreach ($assigned_students as $student): ?>
                                <?php $has_eval = getEvaluation($conn, $student['deployment_id']); ?>
                                <?php if(!$has_eval): ?>
                                    <option value="<?php echo $student['deployment_id']; ?>"><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        
                        <div class="evaluation-grid">
                            <div>
                                <label>Punctuality & Attendance</label>
                                <input type="number" name="punctuality" min="1" max="100" placeholder="0-100" required>
                            </div>
                            <div>
                                <label>Technical Skills</label>
                                <input type="number" name="skills" min="1" max="100" placeholder="0-100" required>
                            </div>
                            <div>
                                <label>Attitude & Teamwork</label>
                                <input type="number" name="attitude" min="1" max="100" placeholder="0-100" required>
                            </div>
                        </div>

                        <label>Overall Supervisor Comments</label>
                        <textarea name="comments" rows="4" style="width: 100%; padding: 15px; border: 2px solid #cbd5e1; border-radius: 8px; margin-bottom: 20px; font-family: inherit; font-size: 1rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='#8b5cf6'" onblur="this.style.borderColor='#cbd5e1'" required></textarea>
                        
                        <button type="submit" name="submit_evaluation" class="btn-clock-in" style="background: linear-gradient(to right, #8b5cf6, #6d28d9); font-size: 1.1rem; padding: 15px; border-radius: 8px;"><i class='bx bx-send'></i> Submit Final Grade</button>
                    </form>
                </div>
            </div>

        </main>
    </div>
    <script src="../assets/js/tabs.js"></script>
</body>
</html>