<?php
/**
 * ONE-CLICK DB Setup - Student Organization Tracker
 * Visit: http://localhost/studentorganizationtracker/setup.php
 * Then DELETE this file for security
 */

require_once 'config/db.php';

$conn = getDBConnection();
$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "
    DROP TABLE IF EXISTS students;
    DROP TABLE IF EXISTS users;
    
    CREATE DATABASE IF NOT EXISTS student_org_tracker;
    
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    
    CREATE TABLE students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20),
        student_id VARCHAR(20) UNIQUE NOT NULL,
        major VARCHAR(50),
        year ENUM('Freshman', 'Sophomore', 'Junior', 'Senior'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    
    INSERT INTO users (username, password, role) VALUES 
    ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin');
    ";
    
    if ($conn->multi_query($sql)) {
        $output = '<div class="alert success"><h3>✅ Database Setup Complete!</h3><p>Tables created. Test user: <strong>admin / admin123</strong></p><p><a href="auth/login.php" class="btn btn-primary">Go to Login</a></p></div>';
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
    } else {
        $output = '<div class="alert error">Error: ' . $conn->error . '</div>';
    }
}

?>
<?php include 'includes/header.php'; ?>
<div class="container">
    <h2>Database Setup</h2>
    <?php echo $output ?: '<p>Click setup to create tables. <strong>Delete this file after!</strong></p>'; ?>
    <?php if (!$output): ?>
    <form method="POST">
        <button type="submit" class="btn btn-primary">Setup Database</button>
    </form>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
