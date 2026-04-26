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

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['student_name']);
    $course = trim($_POST['course']);
    $year = $_POST['year_level'];
    $email = trim($_POST['email']);

    if (empty($name) || empty($course) || empty($year) || empty($email)) {
        $error = "All fields are required.";
    } else {

        $stmt = $conn->prepare("UPDATE students SET student_name=?, course=?, year_level=?, email=? WHERE student_id=?");
        $stmt->bind_param("ssisi", $name, $course, $year, $email, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Student updated successfully!";
            header('Location: index.php');
            exit;
        } else {
            $error = "Update failed.";
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

    <?php if (!empty($error)): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="student_name" value="<?php echo $student['student_name']; ?>" required>
        <input type="text" name="course" value="<?php echo $student['course']; ?>" required>
        <input type="number" name="year_level" value="<?php echo $student['year_level']; ?>" required>
        <input type="email" name="email" value="<?php echo $student['email']; ?>" required>

        <button type="submit">Update</button>
        <a href="index.php">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
