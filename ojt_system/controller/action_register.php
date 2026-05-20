<?php
session_start();
require_once '../model/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_btn'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
        $_SESSION['error'] = 'Please fill out all registration fields.';
        header('Location: ../view/register.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: ../view/register.php');
        exit();
    }

    $email = strtolower($email);
    $sql = "SELECT user_id FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $_SESSION['error'] = 'That email address is already registered.';
        $stmt->close();
        header('Location: ../view/register.php');
        exit();
    }

    $stmt->close();
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Users (email, password_hash, first_name, last_name, role, status) VALUES (?, ?, ?, ?, 'Student', 'Pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $email, $password_hash, $first_name, $last_name);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration received. Your account will be approved by an administrator.';
        $stmt->close();
        header('Location: ../view/login.php');
        exit();
    }

    $_SESSION['error'] = 'Registration failed. Please try again later.';
    $stmt->close();
}

header('Location: ../view/register.php');
exit();
