<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    $_SESSION['error'] = 'Invalid student ID.';
    header('Location: index.php');
    exit;
}

$student = null;
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    $_SESSION['error'] = 'Student not found.';
    header('Location: index.php');
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
        $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, email=?, phone=?, student_id=?, major=?, year=? WHERE id=?");
        $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $phone, $student_id, $major, $year, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Student updated successfully!';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Error: ' . $conn->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2>Edit Student</h2>
    
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>First Name *</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Student ID *</label>
                <input type="text" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Major</label>
                <input type="text" name="major" value="<?php echo htmlspecialchars($student['major'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Year</label>
                <select name="year">
                    <option value="">Select</option>
                    <option <?php echo ($student['year'] == 'Freshman') ? 'selected' : ''; ?>>Freshman</option>
                    <option <?php echo ($student['year'] == 'Sophomore') ? 'selected' : ''; ?>>Sophomore</option>
                    <option <?php echo ($student['year'] == 'Junior') ? 'selected' : ''; ?>>Junior</option>
                    <option <?php echo ($student['year'] == 'Senior') ? 'selected' : ''; ?>>Senior</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Student</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>

