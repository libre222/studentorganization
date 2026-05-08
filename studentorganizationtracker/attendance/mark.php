<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$conn     = getDBConnection();
$students = $conn->query("SELECT student_id, student_name FROM students ORDER BY student_name ASC");
$events   = $conn->query("SELECT e.event_id, e.event_name, o.org_name FROM events e JOIN organizations o ON e.org_id = o.org_id ORDER BY e.event_date DESC");

$student_list = $students ? $students->fetch_all(MYSQLI_ASSOC) : [];
$event_list   = $events   ? $events->fetch_all(MYSQLI_ASSOC)   : [];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id  = $_POST['student_id'];
    $event_id    = $_POST['event_id'];
    $status      = $_POST['status'];
    $date_marked = $_POST['date_marked'];

    if (empty($student_id) || empty($event_id) || empty($status)) {
        $error = 'Please fill in all required fields.';
    } else {
        $check = $conn->prepare("SELECT attendance_id FROM attendance WHERE student_id = ? AND event_id = ?");
        $check->bind_param("ii", $student_id, $event_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $error = 'Attendance for this student at this event has already been recorded.';
        } else {
            $stmt = $conn->prepare("INSERT INTO attendance (student_id, event_id, status, date_marked) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $student_id, $event_id, $status, $date_marked);

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Attendance marked successfully!';
                header('Location: index.php');
                exit;
            } else {
                $error = 'Failed to record attendance.';
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>
<?php include '../includes/header.php'; ?>

<div class="page-header">
    <div>
        <h2>Mark Attendance</h2>
        <p>Record a student's attendance for an event.</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <span class="material-symbols-outlined">arrow_back</span> Back to Attendance
    </a>
</div>

<?php if ($error): ?>
    <div class="alert error"><span class="material-symbols-outlined">error</span> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (empty($student_list) || empty($event_list)): ?>
    <div class="alert warning">
        <span class="material-symbols-outlined">warning</span>
        You need at least one <a href="../students/add.php" style="font-weight:600;color:inherit;text-decoration:underline;">student</a>
        and one <a href="../events/add.php" style="font-weight:600;color:inherit;text-decoration:underline;">event</a> before marking attendance.
    </div>
<?php else: ?>

<div class="card" style="max-width:680px;">
    <div class="card-header">
        <h3>Attendance Details</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-grid">

                <div class="form-group full-width">
                    <label>Student <span class="req">*</span></label>
                    <select name="student_id" required>
                        <option value="">— Select Student —</option>
                        <?php foreach ($student_list as $s): ?>
                            <option value="<?php echo $s['student_id']; ?>"
                                <?php echo (($_POST['student_id'] ?? '') == $s['student_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['student_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Event <span class="req">*</span></label>
                    <select name="event_id" required>
                        <option value="">— Select Event —</option>
                        <?php foreach ($event_list as $e): ?>
                            <option value="<?php echo $e['event_id']; ?>"
                                <?php echo (($_POST['event_id'] ?? '') == $e['event_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($e['event_name'] . ' — ' . $e['org_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status <span class="req">*</span></label>
                    <select name="status" required>
                        <option value="">— Select Status —</option>
                        <?php foreach (['Present', 'Absent', 'Late'] as $st): ?>
                            <option value="<?php echo $st; ?>"
                                <?php echo (($_POST['status'] ?? '') === $st) ? 'selected' : ''; ?>>
                                <?php echo $st; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date Marked</label>
                    <input type="date" name="date_marked"
                           value="<?php echo htmlspecialchars($_POST['date_marked'] ?? date('Y-m-d')); ?>">
                </div>

            </div>

            <div class="form-actions" style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <span class="material-symbols-outlined">how_to_reg</span> Mark Attendance
                </button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
