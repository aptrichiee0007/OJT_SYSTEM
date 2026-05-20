<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/admin.php';

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$student_id = $_GET['id'];
$student = getStudentById($conn, $student_id);


if (!$student) {
    header("Location: admin_dashboard.php");
    exit();
}

$deployment = getStudentDeployment($conn, $student_id);
$companies = getCompanies($conn);
$supervisors = getSupervisors($conn);

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - OJT Admin</title>
    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Enforcing the clean sans-serif font seen in your screenshot */
        body, input, select, button {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand"><i class='bx bx-shield-quarter'></i> OJT Admin Portal</div>
        <div class="nav-user">
            <a href="admin_dashboard.php" class="btn-logout" style="background: #ef4444; color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-weight: bold;">← Back to Dashboard</a>
        </div>
    </nav>

    <div class="dashboard-layout">
        
        <aside class="sidebar">
            <button class="tab-btn" onclick="window.location.href='admin_dashboard.php'">
                💼Dashboard Overview
            </button>
            <button class="tab-btn active" onclick="window.location.href='admin_dashboard.php'">
                👥 Student Masterlist
            </button>
            <button class="tab-btn" onclick="window.location.href='admin_dashboard.php'">
                📁 Document Vault
            </button>
            <button class="tab-btn" onclick="window.location.href='admin_dashboard.php'">
                ✅ Pending Approvals
            </button>
            <button class="tab-btn" onclick="window.location.href='admin_dashboard.php'">
                🏢 Deployments
            </button>
            <button class="tab-btn" onclick="window.location.href='admin_dashboard.php'">
                ⚙️ System Setup
            </button>
            <button class="tab-btn" onclick="window.location.href='admin_dashboard.php'">
                🔑 Account Recovery
            </button>
        </aside>

        <main class="content-area">
            <?php echo $message; ?>
            
            <div class="admin-grid" style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-start;">
                
                <div class="admin-column" style="flex: 1; min-width: 350px;">
                    <div class="card admin-card" style="border: 1px solid #0056b3; margin-bottom: 20px;">
                        <div class="card-header" style="background: #fde047; color: #111827; padding: 15px; font-weight: bold; border-radius: 6px 6px 0 0; border-bottom: 1px solid #0056b3;">
                            📝 EDIT PROFILE: <?php echo htmlspecialchars(strtoupper(($student['first_name'] ?? 'David') . ' ' . ($student['last_name'] ?? 'Brown'))); ?>
                        </div>
                        <form action="../controller/action_admin.php" method="POST" class="deployment-form" style="padding: 20px;">
                            <input type="hidden" name="student_id" value="<?php echo $student['user_id']; ?>">
                            
                            <label style="font-weight: bold; font-size: 0.9rem; color: #333;">First Name</label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name'] ?? ''); ?>" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:4px;">
                            
                            <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Last Name</label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name'] ?? ''); ?>" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:4px;">
                            
                            <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Email Address / Username</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:4px;">
                            
                            <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Account Status</label>
                            <select name="status" required style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #cbd5e1; border-radius:4px;">
                                <option value="Active" <?php echo (($student['status'] ?? 'Active') === 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Suspended" <?php echo (($student['status'] ?? '') === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                            
                            <button type="submit" name="update_student_admin" class="btn-clock-in action" style="width: 100%; padding: 12px; font-weight: bold; border-radius: 50px; background: #fbbf24; color: #111827; border: 1px solid #d97706; cursor: pointer; text-transform: uppercase;">
                                UPDATE PROFILE DETAILS
                            </button>
                        </form>
                    </div>

                    <div class="card admin-card" style="border: 1px solid #0056b3; background-color: #fff5f5;">
                        <div class="card-header" style="background: #fde047; color: #111827; padding: 15px; font-weight: bold; border-radius: 6px 6px 0 0; border-bottom: 1px solid #0056b3;">
                            ⚠️ DANGER ZONE
                        </div>
                        <div style="padding: 20px;">
                            <p style="color: #dc2626; font-weight: bold; font-size: 0.9rem; margin-bottom: 20px;">Warning: Deleting a student permanently erases their account, time logs, and deployment data.</p>
                            
                            <form action="../controller/action_admin.php" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this student? This action cannot be undone.');">
                                <input type="hidden" name="student_id" value="<?php echo $student['user_id']; ?>">
                                <button type="submit" name="delete_student_admin" class="btn-clock-out" style="background-color: #dc2626; color: white; width: 100%; padding: 12px; font-weight: bold; border-radius: 50px; border: 1px solid #b91c1c; cursor: pointer; text-transform: uppercase;">
                                    <i class='bx bx-trash'></i> PERMANENTLY DELETE STUDENT
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="admin-column" style="flex: 1; min-width: 350px;">
                    <div class="card admin-card" style="border: 1px solid #0056b3;">
                        <div class="card-header" style="background: #fde047; color: #111827; padding: 15px; font-weight: bold; border-radius: 6px 6px 0 0; border-bottom: 1px solid #0056b3;">
                            🏢 DEPLOYMENT MANAGER
                        </div>
                        <div style="padding: 20px;">
                            
                            <?php if (!$deployment): ?>
                                <div class="alert warning" style="background: #fef9c3; border: 1px solid #fef08a; color: #854d0e; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                                    <i class='bx bx-info-circle'></i> This student is currently NOT deployed.
                                </div>
                                <form action="../controller/action_admin.php" method="POST" class="deployment-form">
                                    <input type="hidden" name="from_edit_page" value="1">
                                    <input type="hidden" name="student_id" value="<?php echo $student['user_id']; ?>">
                                    
                                    <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Select Company</label>
                                    <select name="company_id" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:4px;">
                                        <option value="">Choose...</option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?php echo $company['company_id']; ?>"><?php echo htmlspecialchars($company['company_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Assign Supervisor</label>
                                    <select name="supervisor_id" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:4px;">
                                        <option value="">Choose...</option>
                                        <?php foreach ($supervisors as $supervisor): ?>
                                            <option value="<?php echo $supervisor['user_id']; ?>"><?php echo htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Required Hours</label>
                                    <input type="number" name="required_hours" value="300" required style="width:100%; padding:10px; margin-bottom:25px; border:1px solid #cbd5e1; border-radius:4px;">
                                    
                                    <button type="submit" name="create_deployment" class="btn-clock-in" style="background: #fbbf24; color: #111827; width: 100%; padding: 12px; font-weight: bold; border: 1px solid #d97706; border-radius: 50px; cursor: pointer; text-transform: uppercase;">
                                        🚀 Deploy Student
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert success" style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                                    <i class='bx bx-check-circle'></i> Currently Deployed! (ID: #<?php echo $deployment['deployment_id']; ?>)
                                </div>
                                <form action="../controller/action_admin.php" method="POST" class="deployment-form">
                                    <input type="hidden" name="student_id" value="<?php echo $student['user_id']; ?>">
                                    <input type="hidden" name="deployment_id" value="<?php echo $deployment['deployment_id']; ?>">
                                    
                                    <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Change Company</label>
                                    <select name="company_id" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:4px;">
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?php echo $company['company_id']; ?>" <?php echo ($deployment['company_id'] == $company['company_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($company['company_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Change Supervisor</label>
                                    <select name="supervisor_id" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #cbd5e1; border-radius:4px;">
                                        <?php foreach ($supervisors as $supervisor): ?>
                                            <option value="<?php echo $supervisor['user_id']; ?>" <?php echo ($deployment['supervisor_id'] == $supervisor['user_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($supervisor['first_name'] . ' ' . $supervisor['last_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                    <label style="font-weight: bold; font-size: 0.9rem; color: #333;">Update Target Hours</label>
                                    <input type="number" name="required_hours" value="<?php echo htmlspecialchars($deployment['required_hours']); ?>" required style="width:100%; padding:10px; margin-bottom:25px; border:1px solid #cbd5e1; border-radius:4px;">
                                    
                                    <button type="submit" name="update_deployment_admin" class="btn-clock-in action" style="width: 100%; padding: 12px; font-weight: bold; border-radius: 50px; background: #fbbf24; color: #111827; border: 1px solid #d97706; cursor: pointer; text-transform: uppercase;">
                                        <i class='bx bx-save'></i> SAVE DEPLOYMENT CHANGES
                                    </button>
                                </form>

                                <hr style="border: none; border-top: 1px dashed #cbd5e1; margin: 30px 0;">

                                <form action="../controller/action_admin.php" method="POST" onsubmit="return confirm('Remove deployment? The student will lose access to their timesheet.');">
                                    <input type="hidden" name="student_id" value="<?php echo $student['user_id']; ?>">
                                    <input type="hidden" name="deployment_id" value="<?php echo $deployment['deployment_id']; ?>">
                                    <button type="submit" name="remove_deployment_admin" class="btn-small action" style="width: 100%; padding: 12px; font-size: 0.9rem; border: 1px dashed #94a3b8; background: transparent; color: #475569; border-radius: 50px; font-weight: bold; cursor: pointer;">
                                        <i class='bx bx-unlink'></i> Remove Deployment
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>w