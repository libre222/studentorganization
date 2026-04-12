<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $student_id = trim($_POST['student_id']);
    $major = trim($_POST['major']);
    $year = $_POST['year'];
    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($student_id)) {
        $error = 'Please fill required fields.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, email, phone, student_id, major, year) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $first_name, $last_name, $email, $phone, $student_id, $major, $year);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Student added successfully!';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Error: ' . $conn->error;
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2>Add Student</h2>
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" required>
            </div>
            <div class="form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" required>
            </div>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone">
            </div>
            <div class="form-group">
                <label>Student ID *</label>
                <input type="text" name="student_id" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Major</label>
                <input type="text" name="major">
            </div>
            <div class="form-group">
                <label>Year</label>
                <select name="year">
                    <option value="">Select</option>
                    <option>Freshman</option>
                    <option>Sophomore</option>
                    <option>Junior</option>
                    <option>Senior</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

