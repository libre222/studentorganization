<?php
session_start();
require_once '../config/db.php';

// Auth check
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Flash messages
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$conn = getDBConnection();
$result = $conn->query("SELECT * FROM students ORDER BY last_name, first_name");
$students = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$conn->close();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h2>Students</h2>
    
    <?php if ($success): ?>
        <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <a href="add.php" class="btn btn-primary">Add New Student</a>
    
    <?php if (empty($students)): ?>
        <p>No students found.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Student ID</th>
                    <th>Phone</th>
                    <th>Major</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['phone'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['major'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($student['year'] ?? ''); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                            <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

