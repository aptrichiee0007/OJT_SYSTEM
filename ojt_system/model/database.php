<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "ojt_system_db";


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    
    error_log("Database connection failed: " . $e->getMessage());
    
    die("A database connection error occurred. Please try again later.");
}
?>