<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = getDBConnection();

// SEARCH
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $conn->prepare("
        SELECT * FROM students 
        WHERE student_name LIKE ? 
        OR course LIKE ? 
        OR email LIKE ?
        ORDER BY student_name ASC
    ");
    
    $like = "%$search%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM students ORDER BY student_name ASC");
}

$students = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

<h2>Students</h2>

<!-- SEARCH BAR -->
<form method="GET" class="search-bar">
    <input 
        type="text" 
        name="search" 
        placeholder="Search students..." 
        value="<?php echo htmlspecialchars($search); ?>"
    >
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="index.php" class="btn btn-secondary">Reset</a>
</form>

<a href="add.php" class="btn btn-primary">Add Student</a>

<br><br>

<table class="data-table">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Course</th>
    <th>Year</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php if (empty($students)): ?>
<tr>
    <td colspan="6">No students found.</td>
</tr>
<?php else: ?>

<?php foreach ($students as $s): ?>
<tr>
    <td><?php echo htmlspecialchars($s['student_id']); ?></td>
    <td><?php echo htmlspecialchars($s['student_name']); ?></td>
    <td><?php echo htmlspecialchars($s['course']); ?></td>
    <td><?php echo htmlspecialchars($s['year_level']); ?></td>
    <td><?php echo htmlspecialchars($s['email']); ?></td>
    <td>
        <a href="edit.php?id=<?php echo $s['student_id']; ?>" class="btn btn-small btn-secondary">Edit</a>
        <a href="delete.php?id=<?php echo $s['student_id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this student?')">Delete</a>
    </td>
</tr>
<?php endforeach; ?>

<?php endif; ?>

</table>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
