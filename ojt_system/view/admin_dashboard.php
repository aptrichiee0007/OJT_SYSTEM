<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/admin.php';

$first_name = $_SESSION['first_name'];
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$pending_users = getPendingUsers($conn);
$undeployed_students = getUndeployedStudents($conn);
$companies = getCompanies($conn);
$supervisors = getSupervisors($conn);
$deployments = getActiveDeployments($conn);
$all_users_directory = getAllUsersDirectory($conn);
$all_students = getAllStudents($conn);
$pending_docs = getPendingDocuments($conn);

$approval_count = count($pending_users);

$deployed_count = count($deployments);
$undeployed_count = count($undeployed_students);
$pending_account_count = count($pending_users);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <i></i> OJT Admin Portal
        </div>
        <div class="nav-user">
            <span>Welcome, <b><?php echo htmlspecialchars($first_name); ?></b></span>
            <a href="../controller/logout.php" class="btn-logout"><i class='bx bx-log-out'></i> Sign Out</a>
        </div>
    </nav>

    <div class="dashboard-layout">
<aside class="sidebar">
    <button class="tab-btn active" onclick="openTab(event, 'tab-overview')">
        💼 Dashboard Overview
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-masterlist')">
        Student Masterlist
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-docs')">
        📄 Document Vault 
        <?php if(count($pending_docs) > 0) echo "<span style='background:#ef4444; color:white; padding:2px 8px; border-radius:12px; font-size:0.8rem; float:right;'>".count($pending_docs)."</span>"; ?>
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-approvals')">
        Pending Approvals
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-deployments')">
        Deployments
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-setup')">
        System Setup
    </button>
    
    <button class="tab-btn" onclick="openTab(event, 'tab-recovery')">
        Account Recovery
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
                        <h3><?php echo count($all_students); ?></h3>
                        <p>Total Enrolled</p>
                    </div>
                    <div class="stat-card">
                        <i class='bx bx-building icon' style="color: #28a745;"></i>
                        <h3><?php echo $deployed_count; ?></h3>
                        <p>Active Deployments</p>
                    </div>
                    <div class="stat-card">
                        <i class='bx bx-file icon' style="color: #ffc107;"></i>
                        <h3><?php echo count($pending_docs); ?></h3>
                        <p>Pending Documents</p>
                    </div>
                </div>

                <div class="admin-grid" style="max-width: 1200px; margin: 30px auto 0 auto;">
                    
                    <div class="admin-column" style="flex: 1.5;">
                        <div class="card" style="height: 100%;">
                            <h3 style="margin-top: 0;"><i class='bx bx-pie-chart-alt-2'></i> System Status</h3>
                            <div style="height: 250px; display: flex; justify-content: center;">
                                <canvas id="deploymentChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="admin-column" style="flex: 2;">
                        <div class="card" style="height: 100%;">
                            <h3 style="margin-top: 0; background-color: #dc3545; color: white; border-bottom: none;"><i class='bx bx-bell bx-tada'></i> Needs Immediate Attention</h3>
                            
                            <div class="info-grid" style="max-height: 250px; overflow-y: auto; padding-right: 10px;">
                                <?php if (empty($pending_docs) && empty($pending_users)): ?>
                                    <div class="info-card full-width" style="text-align: center; border-left-color: #28a745;">
                                        <p style="color: #64748b; margin-top: 15px;"><i class='bx bx-party' style="font-size: 2rem;"></i><br>All caught up! No pending tasks.</p>
                                    </div>
                                <?php else: ?>
                                    
                                    <?php foreach ($pending_users as $user): ?>
                                        <div class="info-card full-width" style="border-left-color: #ffc107; display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <label><i class='bx bx-user-plus'></i> Pending Account Approval</label>
                                                <p style="font-size: 1rem;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</p>
                                            </div>
                                            <button onclick="document.querySelector('[onclick*=\'tab-approvals\']').click()" class="btn-small success">Review</button>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php foreach ($pending_docs as $doc): ?>
                                        <div class="info-card full-width" style="border-left-color: #17a2b8; display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <label><i class='bx bx-file'></i> Pending Document: <?php echo htmlspecialchars($doc['document_type']); ?></label>
                                                <p style="font-size: 1rem;"><?php echo htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']); ?></p>
                                            </div>
                                            <button onclick="document.querySelector('[onclick*=\'tab-docs\']').click()" class="btn-small success" style="background-color: #17a2b8; color: white;">Review</button>
                                        </div>
                                    <?php endforeach; ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="tab-masterlist" class="tab-content" style="display: none;">
                <div class="card admin-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; background-color: #ffe066; margin: -30px -30px 20px -30px; padding: 15px 30px; border-bottom: 3px solid #0056b3; border-radius: 5px 5px 0 0;">
    <h3 style="margin: 0 !important; border: none !important; padding: 0 !important; display: flex; justify-content: center; align-items: center; gap: 8px; width: 100%;">
    <i class='bx bx-list-ul'></i> Enrolled Students Masterlist
</h3>
    <a href="../controller/export_masterlist.php" class="btn-small" style="background: #0056b3; color: white; border-color: #004494; text-decoration: none;"><i class='bx bx-export'></i> Export CSV</a>
</div>
                    <?php if (empty($all_students)): ?>
                        <div class="empty-state">
                            <i class='bx bx-group'></i>
                            <p>No students enrolled yet.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Email Address</th>
                                    <th>Account Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_students as $student): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #1e293b;">
                                        <a href="edit_student.php?id=<?php echo $student['user_id']; ?>" style="color: #0056b3; text-decoration: none; font-size: 1.05rem; border-bottom: 1px dashed #0056b3;">
                                            <?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td>
                                        <?php if ($student['status'] === 'Active'): ?>
                                            <span class="status-badge success">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($student['deployment_id']): ?>
                                            <a href="view_student.php?id=<?php echo $student['deployment_id']; ?>" class="btn-small success" style="text-decoration: none;"><i class='bx bx-file'></i> View File</a>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">Not Deployed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <button class="floating-btn" onclick="openModal('addStudentModal')" title="Add New Students"><i class='bx bx-plus'></i></button>

                <div id="addStudentModal" class="modal">
                    <div class="modal-content card">
                        <span class="close-btn" onclick="closeModal('addStudentModal')">&times;</span>
                        <h3 style="margin-top:0; border-bottom: none; text-align: center; background: transparent; padding: 0;"><i class='bx bx-user-plus'></i> Add Students</h3>

                        <div class="modal-tabs">
                            <button id="btn-single" class="modal-tab-btn active" onclick="switchModalTab('single')"><i class='bx bx-user'></i> Single Student</button>
                            <button id="btn-bulk" class="modal-tab-btn" onclick="switchModalTab('bulk')"><i class='bx bx-file'></i> Bulk Import (CSV)</button>
                        </div>

                        <div id="form-single" style="display: block;">
                            <form action="../controller/action_admin.php" method="POST" class="deployment-form">
                                <label>First Name</label>
                                <input type="text" name="first_name" required>
                                
                                <label>Last Name</label>
                                <input type="text" name="last_name" required>
                                
                                <label>Email Address</label>
                                <input type="email" name="email" required>
                                
                                <label>Temporary Password</label>
                                <input type="text" name="password" required>
                                
                                <button type="submit" name="add_single_student" class="btn-clock-in action"><i class='bx bx-plus-circle'></i> Add Student</button>
                            </form>
                        </div>

                        <div id="form-bulk" style="display: none; text-align: center;">
                            <p style="color: #6c757d; font-size: 0.9rem; margin-bottom: 20px;">Upload a .CSV file with exactly 4 columns: <br><b style="color: #0056b3;">First Name | Last Name | Email | Password</b></p>
                            <form action="../controller/action_admin.php" method="POST" enctype="multipart/form-data" class="deployment-form">
                                <input type="file" name="csv_file" accept=".csv" required style="padding: 15px; border: 2px dashed #0056b3; background-color: #f8f9fa;">
                                <button type="submit" name="import_students" class="btn-clock-in success"><i class='bx bx-upload'></i> Upload & Import CSV</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-docs" class="tab-content" style="display: none;">
                <div class="card admin-card">
                    <h3><i class='bx bx-folder'></i> Document Vault (Pending Review)</h3>
                    <?php if(empty($pending_docs)): ?>
                        <div class="empty-state">
                            <i class='bx bx-check-shield'></i>
                            <p>No documents pending review.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead><tr><th>Student</th><th>Document Type</th><th>File</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach($pending_docs as $doc): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($doc['first_name'].' '.$doc['last_name']); ?></td>
                                    <td style="font-weight: bold;"><?php echo htmlspecialchars($doc['document_type']); ?></td>
                                    <td><a href="../uploads/<?php echo $doc['file_path']; ?>" target="_blank" class="btn-small action" style="text-decoration:none;"><i class='bx bx-file'></i> View File</a></td>
                                    <td>
                                        <form action="../controller/action_documents.php" method="POST" style="display:inline-block;">
                                            <input type="hidden" name="doc_id" value="<?php echo $doc['doc_id']; ?>">
                                            <button type="submit" name="approve_document" class="btn-small success"><i class='bx bx-check'></i> Approve</button>
                                            <button type="submit" name="reject_document" class="btn-small" style="background:#dc3545; color:white;"><i class='bx bx-x'></i> Reject</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-approvals" class="tab-content" style="display: none;">
                <div class="card admin-card">
                    <h3><i class='bx bx-user-check'></i> Pending Account Approvals</h3>
                    <?php if (empty($pending_users)): ?>
                        <div class="empty-state">
                            <i class='bx bx-check-shield'></i>
                            <p>No pending accounts waiting for approval.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_users as $user): ?>
                                <tr>
                                    <td style="font-weight: bold;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><span class="status-badge neutral"><?php echo htmlspecialchars($user['role']); ?></span></td>
                                    <td>
                                        <form action="../controller/action_admin.php" method="POST">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" name="approve_user" class="btn-small success"><i class='bx bx-check'></i> Approve Account</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-deployments" class="tab-content" style="display: none;">
                <div class="admin-grid">
                    <div class="admin-column" style="flex: 1;">
                        <div class="card admin-card">
                            <h3><i class='bx bx-building'></i> Create New Deployment</h3>
                            <form action="../controller/action_admin.php" method="POST" class="deployment-form">
                                <label>Select Student</label>
                                <select name="student_id" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($undeployed_students as $student): ?>
                                        <option value="<?php echo $student['user_id']; ?>"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Select Company</label>
                                <select name="company_id" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($companies as $company): ?>
                                        <option value="<?php echo $company['company_id']; ?>"><?php echo htmlspecialchars($company['company_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Select Supervisor</label>
                                <select name="supervisor_id" required>
                                    <option value="">Choose...</option>
                                    <?php foreach ($supervisors as $supervisor): ?>
                                        <option value="<?php echo $supervisor['user_id']; ?>"><?php echo htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Target Hours</label>
                                <input type="number" name="required_hours" value="300" required>
                                <button type="submit" name="create_deployment" class="btn-clock-in">Deploy Student</button>
                            </form>
                        </div>
                    </div>
                    <div class="admin-column" style="flex: 2;">
                        <div class="card admin-card">
                            <h3><i class='bx bx-map-pin'></i> Active Deployments</h3>
                            <?php if (empty($deployments)): ?>
                                <div class="empty-state">
                                    <i class='bx bx-ghost'></i>
                                    <p>No active deployments.</p>
                                </div>
                            <?php else: ?>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Company</th>
                                            <th>Supervisor</th>
                                            <th>Profile</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($deployments as $deploy): ?>
                                        <tr>
                                            <td style="font-weight: bold;"><?php echo htmlspecialchars($deploy['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($deploy['company_name']); ?></td>
                                            <td><?php echo htmlspecialchars($deploy['supervisor_name']); ?></td>
                                            <td><a href="view_student.php?id=<?php echo $deploy['deployment_id']; ?>" class="btn-small action" style="text-decoration:none;"><i class='bx bx-folder-open'></i> View File</a></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-setup" class="tab-content" style="display: none;">
                <div class="admin-grid">
                    <div class="admin-column">
                        <div class="card admin-card">
                            <h3><i class='bx bx-buildings'></i> Add New Company</h3>
                            <form action="../controller/action_admin.php" method="POST" class="deployment-form">
                                <label>Company Name</label>
                                <input type="text" name="company_name" required>
                                <label>Company Address</label>
                                <input type="text" name="address" required>
                                <label>Contact Number</label>
                                <input type="text" name="contact_number" required>
                                <button type="submit" name="add_company" class="btn-clock-in action">Add Company</button>
                            </form>
                        </div>
                        <div class="card admin-card">
                            <h3><i class='bx bx-list-ul'></i> Company Directory</h3>
                            <?php if (empty($companies)): ?>
                                <div class="empty-state">
                                    <i class='bx bx-building-house'></i>
                                    <p>No companies registered yet.</p>
                                </div>
                            <?php else: ?>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Company Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($companies as $company): ?>
                                        <tr>
                                            <td style="color: #6c757d;">#<?php echo $company['company_id']; ?></td>
                                            <td style="font-weight: bold;"><?php echo htmlspecialchars($company['company_name']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="admin-column">
                        <div class="card admin-card">
                            <h3><i class='bx bx-user-plus'></i> Create Supervisor Account</h3>
                            <form action="../controller/action_admin.php" method="POST" class="deployment-form">
                                <label>First Name</label>
                                <input type="text" name="first_name" required>
                                <label>Last Name</label>
                                <input type="text" name="last_name" required>
                                <label>Email / Username</label>
                                <input type="text" name="username" required>
                                <label>Set Temporary Password</label>
                                <input type="text" name="password" required>
                                <button type="submit" name="add_supervisor" class="btn-clock-in action">Generate Account</button>
                            </form>
                        </div>
                        <div class="card admin-card">
                            <h3><i class='bx bx-group'></i> Supervisor Directory</h3>
                            <?php if (empty($supervisors)): ?>
                                <div class="empty-state">
                                    <i class='bx bx-user-x'></i>
                                    <p>No supervisors created yet.</p>
                                </div>
                            <?php else: ?>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($supervisors as $supervisor): ?>
                                        <tr>
                                            <td style="font-weight: bold;"><?php echo htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']); ?></td>
                                            <td><span class="status-badge success">Active</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        

            <div id="tab-recovery" class="tab-content" style="display: none;">
                <div class="card admin-card">
                    <h3><i class='bx bx-key'></i> Account Directory & Password Recovery</h3>
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 25px; border-radius: 8px; border: 2px solid #e2e8f0;">
                        <table class="data-table" style="margin-top: 0;">
                            <thead style="background-color: #f8f9fa; position: sticky; top: 0; box-shadow: 0 2px 2px rgba(0,0,0,0.1);">
                                <tr>
                                    <th>Name</th>
                                    <th>Username / Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_users_directory as $u): ?>
                                <tr>
                                    <td style="font-weight: bold;"><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                                    <td style="color: #0056b3; font-weight: bold;"><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td><span class="status-badge neutral"><?php echo htmlspecialchars($u['role']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <h4 style="margin-top:0; border-top: 2px solid #dee2e6; padding-top: 20px; color: #dc3545;">Force Password Reset</h4>
                    <form action="../controller/action_admin.php" method="POST" class="deployment-form" style="margin-bottom: 0;">
                        <label>Select Account</label>
                        <select name="target_user_id" required>
                            <option value="">Choose user...</option>
                            <?php foreach ($all_users_directory as $u): ?>
                                <option value="<?php echo $u['user_id']; ?>"><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name'] . ' (' . $u['role'] . ')'); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>New Temporary Password</label>
                        <input type="text" name="new_password" required>
                        <button type="submit" name="reset_password" class="btn-clock-out"><i class='bx bx-reset'></i> Force Reset Password</button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script src="../assets/js/tabs.js"></script>
    <script>
        // Modal functions
        function openModal(modalId) { document.getElementById(modalId).style.display = "block"; }
        function closeModal(modalId) { document.getElementById(modalId).style.display = "none"; }
        function switchModalTab(tabType) {
            if (tabType === 'single') {
                document.getElementById('form-single').style.display = 'block';
                document.getElementById('form-bulk').style.display = 'none';
                document.getElementById('btn-single').classList.add('active');
                document.getElementById('btn-bulk').classList.remove('active');
            } else {
                document.getElementById('form-single').style.display = 'none';
                document.getElementById('form-bulk').style.display = 'block';
                document.getElementById('btn-bulk').classList.add('active');
                document.getElementById('btn-single').classList.remove('active');
            }
        }
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = "none";
            }
        }

        // Initialize Chart.js exactly where it belongs
        document.addEventListener("DOMContentLoaded", function() {
            const chartElement = document.getElementById('deploymentChart');
            if (chartElement) {
                const ctx = chartElement.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Deployed (Active)', 'Undeployed Students', 'Pending Accounts'],
                        datasets: [{
                            data: [
                                <?php echo $deployed_count; ?>, 
                                <?php echo $undeployed_count; ?>, 
                                <?php echo $pending_account_count; ?>
                            ],
                            backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>