<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name']);
    $email        = trim($_POST['email']);
    $student_id   = trim($_POST['student_id']);
    $course       = trim($_POST['course']);
    $year_level   = $_POST['year_level'];

    if (empty($student_name) || empty($email) || empty($student_id)) {
        $error = 'Please fill in all required fields.';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO students (student_id, student_name, course, year_level, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $student_id, $student_name, $course, $year_level, $email);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Student added successfully!';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Failed to add student. The Student ID may already exist.';
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Add New Student</h2>
        <p>Fill in the details below to register a student.</p>
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
                           placeholder="e.g. Juan Dela Cruz"
                           value="<?php echo htmlspecialchars($_POST['student_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>Email Address <span class="req">*</span></label>
                    <input type="email" name="email"
                           placeholder="student@school.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Student ID <span class="req">*</span></label>
                    <input type="text" name="student_id"
                           placeholder="e.g. 2024-00001"
                           value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Course</label>
                    <input type="text" name="course"
                           placeholder="e.g. BSIT"
                           value="<?php echo htmlspecialchars($_POST['course'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Year Level</label>
                    <select name="year_level">
                        <option value="">— Select Year —</option>
                        <?php foreach (['1st Year','2nd Year','3rd Year','4th Year'] as $yr): ?>
                            <option value="<?php echo $yr; ?>"
                                <?php echo (($_POST['year_level'] ?? '') === $yr) ? 'selected' : ''; ?>>
                                <?php echo $yr; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="form-actions" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined"></span> Add Student
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
