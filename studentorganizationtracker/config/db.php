<?php
/**
 * Database Configuration
 * Student Organization Tracker
 */

function getDBConnection() {
    $host = 'localhost';
    $dbname = 'student_org_tracker';
    $username = 'root';
    $password = '';  // Default XAMPP/MySQL password

    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    return $conn;
}
?>

