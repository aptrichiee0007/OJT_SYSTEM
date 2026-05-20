<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../view/login.php");
    exit();
}

require_once '../model/database.php';
require_once '../model/admin.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve_user']) && isset($_POST['user_id'])) {
        if (approveUser($conn, $_POST['user_id'])) {
            $_SESSION['message'] = "<div class='alert success'>User approved successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to approve user.</div>";
        }
    } elseif (isset($_POST['create_deployment'])) {
        $student_id = $_POST['student_id'];
        $company_id = $_POST['company_id'];
        $supervisor_id = $_POST['supervisor_id'];
        $hours = $_POST['required_hours'];
        
        if (createDeployment($conn, $student_id, $company_id, $supervisor_id, $hours)) {
            $_SESSION['message'] = "<div class='alert success'>Student deployed successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to deploy student.</div>";
        }
        
        if (isset($_POST['from_edit_page'])) {
            header("Location: ../view/edit_student.php?id=" . $student_id);
            exit();
        }
    } elseif (isset($_POST['add_supervisor'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        try {
            if (addSupervisor($conn, $first_name, $last_name, $username, $password)) {
                $_SESSION['message'] = "<div class='alert success'>Supervisor account created!</div>";
            } else {
                $_SESSION['message'] = "<div class='alert error'>Failed to create Supervisor.</div>";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $_SESSION['message'] = "<div class='alert error'>Error: That username is already taken.</div>";
            }
        }
    } elseif (isset($_POST['add_company'])) {
        $company_name = $_POST['company_name'];
        $address = $_POST['address'];
        $contact_number = $_POST['contact_number'];
        
        if (addCompany($conn, $company_name, $address, $contact_number)) {
            $_SESSION['message'] = "<div class='alert success'>Company added successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to add company.</div>";
        }
    } elseif (isset($_POST['reset_password'])) {
        $user_id = $_POST['target_user_id'];
        $new_password = $_POST['new_password'];
        
        if (resetUserPassword($conn, $user_id, $new_password)) {
            $_SESSION['message'] = "<div class='alert success'>Password reset successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to reset password.</div>";
        }
        } elseif (isset($_POST['add_single_student'])) {
        $fname = $_POST['first_name'];
        $lname = $_POST['last_name'];
        $email = $_POST['email'];
        $pass = $_POST['password'];

        try {
            if (addStudent($conn, $fname, $lname, $email, $pass)) {
                $_SESSION['message'] = "<div class='alert success'>Student $fname $lname added successfully!</div>";
            } else {
                $_SESSION['message'] = "<div class='alert error'>Failed to add student.</div>";
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $_SESSION['message'] = "<div class='alert error'>Error: That email address is already registered.</div>";
            }
        }
    } elseif (isset($_POST['import_students']) && isset($_FILES['csv_file'])) {
        $file = $_FILES['csv_file']['tmp_name'];
        if ($_FILES['csv_file']['size'] > 0 && pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) == 'csv') {
            $file_handle = fopen($file, "r");
            $success_count = 0;
            $error_count = 0;
            $first_row = true;

            while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE) {
                if ($first_row && strtolower(trim($data[0])) == 'first name') {
                    $first_row = false;
                    continue; 
                }
                if (count($data) >= 4) {
                    $fname = trim($data[0]);
                    $lname = trim($data[1]);
                    $email = trim($data[2]);
                    $pass = trim($data[3]);

                    try {
                        if (addStudent($conn, $fname, $lname, $email, $pass)) {
                            $success_count++;
                        }
                    } catch (mysqli_sql_exception $e) {
                        $error_count++; 
                    }
                }
                $first_row = false;
            }
            fclose($file_handle);
            $_SESSION['message'] = "<div class='alert success'>Import Complete! <b>$success_count</b> students added. ($error_count skipped due to duplicate emails).</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Invalid file. Please upload a .CSV file.</div>";
        }
    } elseif (isset($_POST['update_student_admin'])) {
        $uid = $_POST['student_id'];
        $fname = $_POST['first_name'];
        $lname = $_POST['last_name'];
        $email = $_POST['email'];
        $status = $_POST['status'];
        
        if (updateStudentAdmin($conn, $uid, $fname, $lname, $email, $status)) {
            $_SESSION['message'] = "<div class='alert success'>Student Profile updated successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to update profile.</div>";
        }
        header("Location: ../view/edit_student.php?id=" . $uid);
        exit();
    } elseif (isset($_POST['delete_student_admin'])) {
        $uid = $_POST['student_id'];
        if (deleteUser($conn, $uid)) {
            $_SESSION['message'] = "<div class='alert success'>Student permanently deleted from the system.</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to delete student.</div>";
        }
        header("Location: ../view/admin_dashboard.php");
        exit();
    } elseif (isset($_POST['update_deployment_admin'])) {
        $dep_id = $_POST['deployment_id'];
        $cid = $_POST['company_id'];
        $sid = $_POST['supervisor_id'];
        $hrs = $_POST['required_hours'];
        
        if (updateDeploymentAdmin($conn, $dep_id, $cid, $sid, $hrs)) {
            $_SESSION['message'] = "<div class='alert success'>Deployment updated successfully!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to update deployment.</div>";
        }
        header("Location: ../view/edit_student.php?id=" . $_POST['student_id']);
        exit();
    } elseif (isset($_POST['remove_deployment_admin'])) {
        $dep_id = $_POST['deployment_id'];
        if (removeDeployment($conn, $dep_id)) {
            $_SESSION['message'] = "<div class='alert success'>Deployment removed successfully.</div>";
        } else {
            $_SESSION['message'] = "<div class='alert error'>Failed to remove deployment.</div>";
        }
        header("Location: ../view/edit_student.php?id=" . $_POST['student_id']);
        exit();
    }
    
    header("Location: ../view/admin_dashboard.php");
    exit();
}
header("Location: ../view/admin_dashboard.php");
exit();
?>