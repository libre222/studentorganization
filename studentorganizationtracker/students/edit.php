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

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name   = trim($_POST['student_name']);
    $course = trim($_POST['course']);
    $year   = $_POST['year_level'];
    $email  = trim($_POST['email']);

    if (empty($name) || empty($email)) {
        $error = 'Name and email are required.';
    } else {
        $stmt = $conn->prepare("UPDATE students SET student_name=?, course=?, year_level=?, email=? WHERE student_id=?");
        $stmt->bind_param("ssssi", $name, $course, $year, $email, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Student updated successfully!';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Update failed.';
        }
        $stmt->close();
    }
}

$conn->close();
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Edit Student</h2>
        <p>Update the student's information below.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <span class="material-symbols-outlined"></span> Back to Students
    </a>
</div>

<?php if ($error): ?>
    <div class="alert error"><span class="material-symbols-outlined">error</span> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card" style="max-width: 720px;">
    <div class="card-header">
        <h3>Student Information</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-grid">

                <div class="form-group full-width">
                    <label>Full Name <span class="req">*</span></label>
                    <input type="text" name="student_name"
                           value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>Email Address <span class="req">*</span></label>
                    <input type="email" name="email"
                           value="<?php echo htmlspecialchars($student['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Course</label>
                    <input type="text" name="course"
                           value="<?php echo htmlspecialchars($student['course'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Year Level</label>
                    <select name="year_level">
                        <option value="">— Select Year —</option>
                        <?php foreach (['1st Year','2nd Year','3rd Year','4th Year'] as $yr): ?>
                            <option value="<?php echo $yr; ?>"
                                <?php echo (($student['year_level'] ?? '') === $yr) ? 'selected' : ''; ?>>
                                <?php echo $yr; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="form-actions" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined"></span> Save Changes
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
